<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationPipelineTest extends TestCase
{
    use RefreshDatabase;

    private function createStaff(): User
    {
        return User::factory()->staff()->create();
    }

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createUnreceivedItem(array $overrides = []): MasterItem
    {
        return MasterItem::create(array_merge([
            'item_no'    => 'TEST-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'category'   => 'Test Category',
            'name'       => 'Test Item',
            'unit_price' => 100.00,
            'total_price'=> 100.00,
            'quantity'   => 1,
            'status'     => 'belum_diterima',
        ], $overrides));
    }

    private function createReceivedItem(array $overrides = []): MasterItem
    {
        return MasterItem::create(array_merge([
            'item_no'       => 'TEST-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'category'      => 'Test Category',
            'name'          => 'Test Item',
            'unit_price'    => 100.00,
            'total_price'   => 100.00,
            'quantity'      => 1,
            'status'        => 'diterima',
            'received_date' => now(),
        ], $overrides));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T1: Re-verify already-verified item returns error, no dup asset
    // ═══════════════════════════════════════════════════════════════════

    public function test_cannot_verify_already_verified_item(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        // First verify succeeds
        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Test Lab',
        ])->assertSessionHas('success');
        $this->assertEquals('verified', $item->fresh()->status);

        $assetCount = Asset::where('master_item_id', $item->id)->count();

        // Second verify returns error, no duplicate asset
        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Test Lab',
        ])->assertSessionHasErrors('item');

        $this->assertEquals($assetCount, Asset::where('master_item_id', $item->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T2: Receive already-received item returns error
    // ═══════════════════════════════════════════════════════════════════

    public function test_cannot_receive_already_received_item(): void
    {
        $staff = $this->createStaff();
        $item = $this->createReceivedItem();

        $this->actingAs($staff)
            ->post("/verification/{$item->id}/receive", [
                'do_no' => 'DO-TEST',
            ])->assertSessionHasErrors('item');

        $this->assertEquals('diterima', $item->fresh()->status);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T3: Verify belum_diterima item returns error (must receive 1st)
    // ═══════════════════════════════════════════════════════════════════

    public function test_cannot_verify_unreceived_item(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createUnreceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        $this->actingAs($admin)
            ->post("/verification/{$item->id}/verify", [
                'location' => 'Test Lab',
            ])->assertSessionHasErrors('item');

        $this->assertEquals('belum_diterima', $item->fresh()->status);
        $this->assertEquals(0, Asset::where('master_item_id', $item->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T4: Verify at exactly RM 2,000 → fixed_asset (PA-2)
    // ═══════════════════════════════════════════════════════════════════

    public function test_price_at_2000_creates_fixed_asset(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 2000.00, 'total_price' => 2000.00]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Lab A',
        ])->assertSessionHas('success');

        $asset = Asset::where('master_item_id', $item->id)->first();
        $this->assertNotNull($asset);
        $this->assertEquals('fixed_asset', $asset->asset_type);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T5: Verify at RM 1,999.99 → inventory (PA-3)
    // ═══════════════════════════════════════════════════════════════════

    public function test_price_below_2000_creates_inventory(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 1999.99, 'total_price' => 1999.99]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Lab B',
        ])->assertSessionHas('success');

        $asset = Asset::where('master_item_id', $item->id)->first();
        $this->assertNotNull($asset);
        $this->assertEquals('inventory', $asset->asset_type);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T6: Multi-S/N verification creates N assets
    // ═══════════════════════════════════════════════════════════════════

    public function test_multi_serial_split_creates_multiple_assets(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem([
            'serial_numbers' => 'SN001,SN002,SN003',
            'quantity'       => 3,
            'unit_price'     => 5000.00,
            'total_price'    => 15000.00,
        ]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Lab C',
        ])->assertSessionHas('success');

        $assets = Asset::where('master_item_id', $item->id)->orderBy('id')->get();
        $this->assertCount(3, $assets);
        $this->assertEquals('SN001', $assets[0]->serial_number);
        $this->assertEquals('SN002', $assets[1]->serial_number);
        $this->assertEquals('SN003', $assets[2]->serial_number);
        // Each split item has quantity=1
        $this->assertEquals(1, $assets[0]->quantity);
        $this->assertEquals(1, $assets[1]->quantity);
        $this->assertEquals(1, $assets[2]->quantity);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T7: Verify without location defaults to 'TBD'
    // ═══════════════════════════════════════════════════════════════════

    public function test_verify_without_location_defaults_to_tbd(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [])
            ->assertSessionHas('success');

        $asset = Asset::where('master_item_id', $item->id)->first();
        $this->assertEquals('TBD', $asset->location);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T8: No serial_numbers → single asset with empty/null serial
    // ═══════════════════════════════════════════════════════════════════

    public function test_item_without_serial_numbers_creates_single_asset(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem([
            'serial_numbers' => null,
            'unit_price'     => 5000.00,
            'total_price'    => 5000.00,
        ]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Lab D',
        ])->assertSessionHas('success');

        $assets = Asset::where('master_item_id', $item->id)->get();
        $this->assertCount(1, $assets);
        $this->assertTrue(empty($assets[0]->serial_number));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T9: Verify with warranty_expiry stored & formatted correctly
    // ═══════════════════════════════════════════════════════════════════

    public function test_verify_with_warranty_expiry(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);
        $warrantyDate = '2028-06-30';

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location'        => 'Lab E',
            'warranty_expiry' => $warrantyDate,
        ])->assertSessionHas('success');

        $asset = Asset::where('master_item_id', $item->id)->first();
        $this->assertNotNull($asset->warranty_expiry);
        $this->assertEquals($warrantyDate, $asset->warranty_expiry->format('Y-m-d'));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T10: Verify updates master item metadata (status, type, verified_by)
    // ═══════════════════════════════════════════════════════════════════

    public function test_verify_updates_master_item_metadata(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem([
            'unit_price'  => 5000.00,
            'total_price' => 5000.00,
        ]);

        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location'       => 'Lab F',
            'custodian_name' => 'Dr. Ahmad',
        ])->assertSessionHas('success');

        $fresh = $item->fresh();
        $this->assertEquals('verified', $fresh->status);
        $this->assertEquals('fixed_asset', $fresh->asset_type);
        $this->assertNotNull($fresh->verified_at);
        $this->assertEquals($admin->id, $fresh->verified_by);

        $asset = Asset::where('master_item_id', $item->id)->first();
        $this->assertEquals('Dr. Ahmad', $asset->custodian_name);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T11 (formerly T17): Sequential verifications produce unique tags
    // ═══════════════════════════════════════════════════════════════════

    public function test_sequential_verifications_produce_unique_asset_tags(): void
    {
        $admin = $this->createAdmin();

        $item1 = $this->createReceivedItem([
            'serial_numbers' => 'SN100',
            'unit_price'     => 5000.00,
            'total_price'    => 5000.00,
        ]);
        $item2 = $this->createReceivedItem([
            'serial_numbers' => 'SN200',
            'unit_price'     => 3000.00,
            'total_price'    => 3000.00,
        ]);

        $this->actingAs($admin)->post("/verification/{$item1->id}/verify", [
            'location' => 'Lab X',
        ])->assertSessionHas('success');

        $this->actingAs($admin)->post("/verification/{$item2->id}/verify", [
            'location' => 'Lab Y',
        ])->assertSessionHas('success');

        $asset1 = Asset::where('master_item_id', $item1->id)->first();
        $asset2 = Asset::where('master_item_id', $item2->id)->first();

        $this->assertNotNull($asset1->asset_tag);
        $this->assertNotNull($asset2->asset_tag);
        $this->assertNotEquals($asset1->asset_tag, $asset2->asset_tag);

        // Both tags should follow AST-YYYYMMDD-NNNNNN format
        $this->assertMatchesRegularExpression('/^AST-\d{8}-\d{6}$/', $asset1->asset_tag);
        $this->assertMatchesRegularExpression('/^AST-\d{8}-\d{6}$/', $asset2->asset_tag);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T12 (formerly T18): Lock contention — verify while lock held
    // ═══════════════════════════════════════════════════════════════════

    public function test_asset_tag_lock_prevents_race_condition(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        // Acquire the same lock the controller uses — this simulates
        // another process holding the tag-generation lock.
        \Illuminate\Support\Facades\Cache::lock('asset-tag', 10)->block(5);

        // The verify call will try to get the lock (lock timeout = 5s) but
        // we hold it, so it should fail gracefully.
        $this->actingAs($admin)->post("/verification/{$item->id}/verify", [
            'location' => 'Lab Z',
        ]);

        // Item status should NOT have changed (DB transaction rolled back
        // because tag-generation failed inside the try/finally block).
        // Actually, the outer DB::transaction will roll back on exception.
        $this->assertEquals('diterima', $item->fresh()->status);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T13–T17: Tong (soft delete / restore) + permission props
    // ═══════════════════════════════════════════════════════════════════

    /** T13: index() exposes can_verify / can_delete / trashed for UI gating */
    public function test_index_exposes_permission_and_trash_props(): void
    {
        $staff = $this->createStaff();

        $trashedItem = $this->createReceivedItem();
        $trashedItem->delete();

        $this->actingAs($staff)->get('/verification')->assertInertia(fn ($page) => $page
            ->component('Verification/Index')
            ->where('can_verify', true)         // `user` role has `verify`
            ->where('can_delete', false)        // `user` role has no `crud_assets`
            ->where('can_force_delete', false)  // `user` role has no `force_delete`
            ->has('trashed', 1)
        );
    }

    /** T14: admin can soft-delete an item that has no downstream records */
    public function test_admin_can_delete_unreceived_item(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem();

        $this->actingAs($admin)
            ->delete("/verification/{$item->id}")
            ->assertSessionHas('success');

        $this->assertSoftDeleted('master_items', ['id' => $item->id]);
        $this->assertNull(MasterItem::find($item->id));
        $this->assertNotNull(MasterItem::withTrashed()->find($item->id));
    }

    /** T15: deleting an item that already produced a PA-1/asset is refused */
    public function test_cannot_delete_item_with_downstream_records(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        $this->actingAs($admin)
            ->post("/verification/{$item->id}/verify", ['location' => 'Lab A'])
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->delete("/verification/{$item->id}")
            ->assertSessionHasErrors('item');

        $this->assertNotSoftDeleted('master_items', ['id' => $item->id]);
    }

    /** T16: staff cannot delete — route is gated by permission:crud_assets */
    public function test_staff_cannot_delete_item(): void
    {
        $staff = $this->createStaff();
        $item  = $this->createUnreceivedItem();

        $this->actingAs($staff)
            ->delete("/verification/{$item->id}")
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('master_items', ['id' => $item->id]);
    }

    /** T17: restore brings a trashed master item back */
    public function test_admin_can_restore_deleted_item(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem();
        $item->delete();

        $this->actingAs($admin)
            ->post("/verification/{$item->id}/restore")
            ->assertSessionHas('success');

        $this->assertNotSoftDeleted('master_items', ['id' => $item->id]);
    }

    /**
     * T18: a rejected Verify must reach the *page*, not just the session.
     *
     * This is the P2 regression guard. The page used to render the banner from
     * `filters.item_error`, a key `index()` never sends, so every pipeline
     * rejection was a silent no-op in the UI even though the server was
     * correctly putting it in the error bag.
     */
    public function test_rejected_verify_reaches_the_page_props(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createReceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        // First verify succeeds…
        $this->actingAs($admin)
            ->post("/verification/{$item->id}/verify", ['location' => 'Lab A'])
            ->assertSessionHas('success');

        // …re-verifying is rejected. Follow the redirect back to the page and
        // assert the message is present in the Inertia `errors` prop that the
        // React page reads.
        $this->actingAs($admin)
            ->from('/verification')
            ->followingRedirects()
            ->post("/verification/{$item->id}/verify", ['location' => 'Lab A'])
            ->assertInertia(fn ($page) => $page
                ->component('Verification/Index')
                ->has('errors.item')
            );
    }

    // ═══════════════════════════════════════════════════════════════════
    //  T19–T21: permanent delete + audit trail
    // ═══════════════════════════════════════════════════════════════════

    /** T19: only a superadmin can permanently delete from the Tong */
    public function test_only_superadmin_can_force_delete_a_trashed_item(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem();
        $item->delete();

        // admin has `crud_assets` but NOT `force_delete`
        $this->actingAs($admin)
            ->delete("/verification/{$item->id}/force")
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');

        $this->assertNotNull(MasterItem::withTrashed()->find($item->id));

        $super = User::factory()->superadmin()->create();

        $this->actingAs($super)
            ->delete("/verification/{$item->id}/force")
            ->assertSessionHas('success');

        // row is gone for good, and the purge is on the record
        $this->assertNull(MasterItem::withTrashed()->find($item->id));
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'force_deleted',
            'subject_type' => MasterItem::class,
            'subject_id'   => $item->id,
        ]);
    }

    /** T20: moving to / restoring from the Tong is audited with explicit events */
    public function test_tong_operations_are_audited(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem();

        $this->actingAs($admin)->delete("/verification/{$item->id}")->assertSessionHas('success');
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'deleted',
            'subject_type' => MasterItem::class,
            'subject_id'   => $item->id,
        ]);

        $this->actingAs($admin)->post("/verification/{$item->id}/restore")->assertSessionHas('success');
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'restored',
            'subject_type' => MasterItem::class,
            'subject_id'   => $item->id,
        ]);
    }

    /** T21: the pipeline actions themselves (Receive / Verify) are audited */
    public function test_pipeline_actions_are_audited(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem(['unit_price' => 5000.00, 'total_price' => 5000.00]);

        $this->actingAs($admin)
            ->post("/verification/{$item->id}/receive", ['do_no' => 'DO-AUDIT-1'])
            ->assertSessionHas('success');
        $this->actingAs($admin)
            ->post("/verification/{$item->id}/verify", ['location' => 'Lab A'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_log', [
            'event'        => 'received',
            'subject_type' => MasterItem::class,
            'subject_id'   => $item->id,
        ]);
        $this->assertDatabaseHas('activity_log', [
            'event'        => 'verified',
            'subject_type' => MasterItem::class,
            'subject_id'   => $item->id,
        ]);
    }
}
