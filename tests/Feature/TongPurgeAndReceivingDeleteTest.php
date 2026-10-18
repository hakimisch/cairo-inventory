<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\CommitteeAppointment;
use App\Models\DamageReport;
use App\Models\DisposalSale;
use App\Models\MasterItem;
use App\Models\Receiving;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Coverage for the 2026-10-18 delete/purge work:
 *   P1 — a PA-1 can be binned in any status, but never while it owns an asset.
 *   P2 — an asset in the Tong can be purged (superadmin), refusing accountability
 *        records and cascading routine history.
 *   P3 — suppliers, committee appointments and disposal sales can be purged.
 */
class TongPurgeAndReceivingDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function superadmin(): User
    {
        return User::factory()->superadmin()->create();
    }

    private function makeReceiving(array $overrides = []): Receiving
    {
        return Receiving::create(array_merge([
            'receive_no'        => 'REC-T' . mt_rand(100000, 999999),
            'supplier_name'     => 'Test Supplier',
            'supplier_address'  => 'Test Address',
            'purchase_order_no' => 'PO-1',
            'delivery_order_no' => 'DO-1',
            'invoice_no'        => 'INV-1',
            'item_description'  => 'Laptop',
            'quantity_ordered'  => 1,
            'quantity_received' => 1,
            'unit_price'        => 100.00,
            'total_price'       => 100.00,
            'status'            => 'pending',
        ], $overrides));
    }

    private function makeAsset(array $overrides = []): Asset
    {
        return Asset::create(array_merge([
            'asset_tag'      => 'AST-T' . mt_rand(100000, 999999),
            'name'           => 'Laptop',
            'category'       => 'IT',
            'asset_type'     => 'inventory',
            'purchase_price' => 100.00,
            'location'       => 'Lab A',
            'status'         => 'active',
        ], $overrides));
    }

    private function makeDisposalSale(string $reference): DisposalSale
    {
        // asset_disposal_id is a required FK — build the parent disposal first.
        $asset = $this->makeAsset();

        $disposalId = DB::table('asset_disposals')->insertGetId([
            'asset_id'       => $asset->id,
            'request_reason' => 'Rosak',
            'status'         => 'draft',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return DisposalSale::create([
            'asset_disposal_id' => $disposalId,
            'sale_type'         => 'Lelongan',
            'sale_reference'    => $reference,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  P1 — PA-1 (receivings) delete
    // ═══════════════════════════════════════════════════════════════════

    public function test_pending_receiving_can_be_deleted(): void
    {
        $receiving = $this->makeReceiving(['status' => 'pending']);

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}")
            ->assertRedirect('/receivings')
            ->assertSessionHas('success');

        $this->assertSoftDeleted('receivings', ['id' => $receiving->id]);
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'deleted',
            'subject_type' => Receiving::class,
            'subject_id'   => $receiving->id,
        ]);
    }

    public function test_rejected_receiving_can_be_deleted(): void
    {
        // Rejected PA-1s never produced an asset — they used to be undeletable.
        $receiving = $this->makeReceiving(['status' => 'rejected']);

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}")
            ->assertSessionHas('success');

        $this->assertSoftDeleted('receivings', ['id' => $receiving->id]);
    }

    public function test_accepted_receiving_with_a_linked_asset_cannot_be_deleted(): void
    {
        $receiving = $this->makeReceiving(['status' => 'accepted']);
        $asset     = $this->makeAsset(['receiving_id' => $receiving->id]);

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}")
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('receivings', ['id' => $receiving->id]);
        $this->assertNull($asset->fresh()->deleted_at);
    }

    public function test_accepted_receiving_without_an_asset_can_be_deleted(): void
    {
        $receiving = $this->makeReceiving(['status' => 'accepted']);

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}")
            ->assertSessionHas('success');

        $this->assertSoftDeleted('receivings', ['id' => $receiving->id]);
    }

    public function test_trashed_receiving_cannot_be_deleted_while_its_asset_still_exists(): void
    {
        // A soft-deleted asset is still a dependent record: restoring it later must
        // keep its PA-1.
        $receiving = $this->makeReceiving(['status' => 'accepted']);
        $asset     = $this->makeAsset(['receiving_id' => $receiving->id]);
        $asset->delete();

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}")
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('receivings', ['id' => $receiving->id]);
    }

    public function test_only_a_superadmin_can_purge_a_trashed_receiving(): void
    {
        $receiving = $this->makeReceiving();
        $receiving->delete();

        $this->actingAs($this->admin())
            ->delete("/receivings/{$receiving->id}/force")
            ->assertSessionHas('error');

        $this->assertSoftDeleted('receivings', ['id' => $receiving->id]);

        $this->actingAs($this->superadmin())
            ->delete("/receivings/{$receiving->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('receivings', ['id' => $receiving->id]);
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'force_deleted',
            'subject_type' => Receiving::class,
            'subject_id'   => $receiving->id,
        ]);
    }

    public function test_verify_stamps_the_receiving_id_on_the_created_asset(): void
    {
        $masterItem = MasterItem::create([
            'item_no'     => 'TEST-LINK-1',
            'category'    => 'IT',
            'name'        => 'Linked Laptop',
            'unit_price'  => 500.00,
            'total_price' => 500.00,
            'quantity'    => 1,
            'status'      => 'diterima',
        ]);

        $this->actingAs($this->admin())
            ->post("/verification/{$masterItem->id}/verify", ['location' => 'Lab B'])
            ->assertSessionHas('success');

        $receiving = Receiving::where('master_item_id', $masterItem->id)->firstOrFail();
        $asset     = Asset::where('master_item_id', $masterItem->id)->firstOrFail();

        $this->assertSame($receiving->id, $asset->receiving_id);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  P2 — asset permanent delete
    // ═══════════════════════════════════════════════════════════════════

    public function test_asset_purge_requires_force_delete(): void
    {
        $asset = $this->makeAsset();
        $asset->delete();

        $this->actingAs($this->admin())
            ->delete("/assets/{$asset->id}/force")
            ->assertSessionHas('error');

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);

        $this->actingAs($this->superadmin())
            ->delete("/assets/{$asset->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'force_deleted',
            'subject_type' => Asset::class,
            'subject_id'   => $asset->id,
        ]);
    }

    public function test_asset_purge_refuses_a_record_that_is_not_in_the_tong(): void
    {
        $asset = $this->makeAsset();

        $this->actingAs($this->superadmin())
            ->delete("/assets/{$asset->id}/force")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'deleted_at' => null]);
    }

    public function test_asset_purge_cascades_routine_history(): void
    {
        $asset = $this->makeAsset();
        $asset->placements()->create([
            'custodian_name' => 'Ali',
            'location'       => 'Lab A',
            'assigned_date'  => now(),
            'is_lokasi_luar' => false,
        ]);
        $asset->delete();

        $this->actingAs($this->superadmin())
            ->delete("/assets/{$asset->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
        $this->assertSame(0, DB::table('asset_placements')->where('asset_id', $asset->id)->count());
    }

    public function test_asset_purge_is_refused_while_a_damage_report_exists(): void
    {
        $asset = $this->makeAsset();
        DamageReport::create([
            'asset_id'           => $asset->id,
            'reported_by'        => 'Ali',
            'damage_date'        => now()->toDateString(),
            'damage_description' => 'Screen cracked',
            'status'             => 'pending',
        ]);
        $asset->delete();

        $this->actingAs($this->superadmin())
            ->delete("/assets/{$asset->id}/force")
            ->assertSessionHas('error');

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        $this->assertSame(1, DamageReport::withTrashed()->where('asset_id', $asset->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════════
    //  Register counts must describe the rows the table actually shows
    // ═══════════════════════════════════════════════════════════════════

    public function test_receiving_index_stats_exclude_trashed_rows(): void
    {
        $this->makeReceiving(['status' => 'accepted']);
        $this->makeReceiving(['status' => 'pending']);

        $binned = $this->makeReceiving(['status' => 'accepted']);
        $binned->delete();

        $this->actingAs($this->admin())
            ->get('/receivings')
            ->assertInertia(fn ($page) => $page
                ->component('Assets/ReceivingIndex')
                ->where('stats.total', 2)      // was 3 before: total counted the Tong
                ->where('stats.accepted', 1)
                ->where('stats.pending', 1)
                ->where('stats.trashed', 1)
                ->has('receivings', 2)
                ->has('trashed', 1)
            );
    }

    public function test_receiving_index_names_the_asset_that_blocks_the_delete(): void
    {
        $receiving = $this->makeReceiving(['status' => 'accepted']);
        $asset     = $this->makeAsset(['receiving_id' => $receiving->id]);

        $this->actingAs($this->admin())
            ->get('/receivings')
            ->assertInertia(fn ($page) => $page
                ->where('receivings.0.has_asset', true)
                ->where('receivings.0.asset_tags.0', $asset->asset_tag)
            );
    }

    public function test_assets_index_total_excludes_trashed_rows(): void
    {
        $this->makeAsset();

        $binned = $this->makeAsset();
        $binned->delete();

        $this->actingAs($this->admin())
            ->get('/assets')
            ->assertInertia(fn ($page) => $page
                ->component('Assets/Index')
                ->where('stats.total', 1)
                ->has('assets', 1)
                ->has('trashed', 1)
            );
    }

    // ═══════════════════════════════════════════════════════════════════
    //  P3 — suppliers, committee appointments, disposal sales
    // ═══════════════════════════════════════════════════════════════════

    public function test_supplier_purge_is_refused_while_master_items_reference_it(): void
    {
        $supplier = Supplier::create(['name' => 'ACME Sdn Bhd']);

        MasterItem::create([
            'item_no'     => 'SUP-1',
            'category'    => 'IT',
            'name'        => 'Monitor',
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'quantity'    => 1,
            'status'      => 'belum_diterima',
            'supplier_id' => $supplier->id,
        ]);

        $supplier->delete();

        $this->actingAs($this->superadmin())
            ->delete("/suppliers/{$supplier->id}/force")
            ->assertSessionHas('error');

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_supplier_can_be_purged_when_nothing_references_it(): void
    {
        $supplier = Supplier::create(['name' => 'Unused Sdn Bhd']);
        $supplier->delete();

        $this->actingAs($this->superadmin())
            ->delete("/suppliers/{$supplier->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'force_deleted',
            'subject_type' => Supplier::class,
        ]);
    }

    public function test_committee_appointment_can_be_purged(): void
    {
        $asset     = $this->makeAsset();
        $appointment = CommitteeAppointment::create([
            'user_id'          => $this->admin()->id,
            'appointable_type' => Asset::class,
            'appointable_id'   => $asset->id,
            'role'             => 'chairman',
            'valid_from'       => now()->toDateString(),
            'valid_until'      => now()->addYear()->toDateString(),
        ]);
        $appointment->delete();

        $this->actingAs($this->superadmin())
            ->delete("/committee-appointments/{$appointment->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('committee_appointments', ['id' => $appointment->id]);
    }

    public function test_disposal_sale_purge_is_refused_while_items_exist(): void
    {
        $sale = $this->makeDisposalSale('SALE-1');

        DB::table('disposal_sale_items')->insert([
            'disposal_sale_id' => $sale->id,
            'quantity'         => 1,
            'status'           => 'available',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $sale->delete();

        $this->actingAs($this->superadmin())
            ->delete("/disposal-sales/{$sale->id}/force")
            ->assertSessionHas('error');

        $this->assertSoftDeleted('disposal_sales', ['id' => $sale->id]);
        $this->assertSame(1, DB::table('disposal_sale_items')->where('disposal_sale_id', $sale->id)->count());
    }

    public function test_disposal_sale_can_be_purged_when_it_has_no_items(): void
    {
        $sale = $this->makeDisposalSale('SALE-2');
        $sale->delete();

        $this->actingAs($this->superadmin())
            ->delete("/disposal-sales/{$sale->id}/force")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('disposal_sales', ['id' => $sale->id]);
    }
}
