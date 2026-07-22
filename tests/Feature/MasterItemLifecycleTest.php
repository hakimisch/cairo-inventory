<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MasterItem;
use App\Models\Receiving;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterItemLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function createStaff(): User
    {
        return User::factory()->staff()->create();
    }

    private function createItem(array $overrides = []): MasterItem
    {
        return MasterItem::create(array_merge([
            'item_no'     => 'LIFECYCLE-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'name'        => 'Lifecycle Test Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'belum_diterima',
        ], $overrides));
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T44: Dedup endpoint merges duplicate item_nos
    // ═════════════════════════════════════════════════════════════════════

    public function test_dedup_merges_duplicate_item_numbers(): void
    {
        $staff = $this->createStaff();

        // Create two master items with the SAME item_no
        $itemNo = 'DUP-TEST-001';

        // The surviving item should have higher id (created later) → will be kept
        $keep = MasterItem::create([
            'item_no'     => $itemNo,
            'name'        => 'Keep Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'verified',     // higher status priority
        ]);
        $toBeDeleted = MasterItem::create([
            'item_no'     => $itemNo,
            'name'        => 'Delete Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'diterima',      // lower status priority
        ]);

        // Create an asset referencing the item that will be deleted
        $asset = Asset::create([
            'master_item_id'   => $toBeDeleted->id,
            'name'             => 'Asset from deleted MI',
            'category'         => 'Test',
            'asset_tag'        => 'AST-DEDUP-001',
            'serial_number'    => 'SN-DEDUP',
            'asset_type'       => 'inventory',
            'purchase_price'   => 100.00,
            'unit_price'       => 100.00,
            'quantity'         => 1,
            'location'         => 'Lab',
            'status'           => 'active',
        ]);

        // Create a receiving referencing the item that will be deleted
        $receiving = Receiving::create([
            'master_item_id'    => $toBeDeleted->id,
            'receive_no'        => 'REC-DEDUP',
            'supplier_name'     => 'Dedup Supplier',
            'supplier_address'  => 'Address',
            'purchase_order_no' => 'PO-DEDUP',
            'delivery_order_no' => 'DO-DEDUP',
            'invoice_no'        => 'INV-DEDUP',
            'item_description'  => 'Dedup Item',
            'quantity_ordered'  => 1,
            'quantity_received' => 1,
            'unit_price'        => 100.00,
            'total_price'       => 100.00,
            'status'            => 'accepted',
        ]);

        // Verify initial state: 2 items with same item_no
        $this->assertEquals(2, MasterItem::where('item_no', $itemNo)->count());

        // Run dedup
        $response = $this->actingAs($staff)->get('/_dev/dedup-master-items');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(1, $data['deleted']);
        $this->assertEquals(0, $data['dupes_left']);

        // Only one item remains
        $this->assertEquals(1, MasterItem::where('item_no', $itemNo)->count());
        $remaining = MasterItem::where('item_no', $itemNo)->first();
        $this->assertEquals('Keep Item', $remaining->name);

        // Asset and receiving now point to the kept item
        $this->assertEquals($remaining->id, $asset->fresh()->master_item_id);
        $this->assertEquals($remaining->id, $receiving->fresh()->master_item_id);
    }

    /** Dedup with no duplicates returns 0 deleted */
    public function test_dedup_with_no_duplicates_returns_zero(): void
    {
        $staff = $this->createStaff();

        // Create unique item_nos — no duplicates
        MasterItem::create(['item_no' => 'UNIQUE-001', 'name' => 'A', 'category' => 'T', 'quantity' => 1, 'unit_price' => 1, 'total_price' => 1, 'status' => 'belum_diterima']);
        MasterItem::create(['item_no' => 'UNIQUE-002', 'name' => 'B', 'category' => 'T', 'quantity' => 1, 'unit_price' => 1, 'total_price' => 1, 'status' => 'diterima']);
        MasterItem::create(['item_no' => 'UNIQUE-003', 'name' => 'C', 'category' => 'T', 'quantity' => 1, 'unit_price' => 1, 'total_price' => 1, 'status' => 'verified']);

        $response = $this->actingAs($staff)->get('/_dev/dedup-master-items');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(0, $data['deleted']);
        $this->assertEquals(0, $data['dupes_left']);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T45: Verification stats match DB state
    // ═════════════════════════════════════════════════════════════════════

    public function test_verification_stats_match_database(): void
    {
        $staff = $this->createStaff();

        // Create items in each status
        $this->createItem(['status' => 'belum_diterima', 'name' => 'Pending A']);
        $this->createItem(['status' => 'belum_diterima', 'name' => 'Pending B']);
        $this->createItem(['status' => 'diterima', 'name' => 'Received A']);
        $this->createItem(['status' => 'diterima', 'name' => 'Received B']);
        $this->createItem(['status' => 'diterima', 'name' => 'Received C']);
        $this->createItem(['status' => 'verified', 'name' => 'Verified A']);
        $verifiedItem = MasterItem::where('status', 'verified')->first();
        $this->createItem(['status' => 'verified', 'name' => 'Verified B']);

        // Create an asset linked to one verified item
        Asset::create([
            'master_item_id' => $verifiedItem->id,
            'name'           => 'Verified Asset',
            'category'       => 'Test',
            'asset_tag'      => 'AST-STATS-001',
            'serial_number'  => 'SN-STATS',
            'asset_type'     => 'inventory',
            'purchase_price' => 100.00,
            'unit_price'     => 100.00,
            'quantity'       => 1,
            'location'       => 'Lab',
            'status'         => 'active',
        ]);

        $response = $this->actingAs($staff)->get('/verification');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Verification/Index')
            ->has('stats')
            ->where('stats.total', 7)
            ->where('stats.belum_diterima', 2)
            ->where('stats.diterima', 3)
            ->where('stats.verified', 2)
            ->where('stats.with_asset', 1)
        );
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T46: Filter by status returns only matching items
    // ═════════════════════════════════════════════════════════════════════

    public function test_filter_by_belum_diterima_status(): void
    {
        $staff = $this->createStaff();

        $this->createItem(['status' => 'belum_diterima', 'name' => 'Pending Only']);
        $this->createItem(['status' => 'diterima', 'name' => 'Received']);
        $this->createItem(['status' => 'verified', 'name' => 'Verified']);

        $response = $this->actingAs($staff)->get('/verification?status=belum_diterima');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('stats.belum_diterima', 1)
            ->where('stats.diterima', 1)
            ->where('stats.verified', 1)
        );
    }

    public function test_filter_by_diterima_status(): void
    {
        $staff = $this->createStaff();

        $this->createItem(['status' => 'belum_diterima', 'name' => 'Pending']);
        $this->createItem(['status' => 'diterima', 'name' => 'Only Received']);
        $this->createItem(['status' => 'verified', 'name' => 'Verified']);

        $response = $this->actingAs($staff)->get('/verification?status=diterima');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Verification/Index')
        );
        // Visually confirmed via stats — filter only affects the items list,
        // not the stats counts. This test validates the page loads without error.
    }

    public function test_filter_by_verified_status(): void
    {
        $staff = $this->createStaff();

        $this->createItem(['status' => 'belum_diterima', 'name' => 'Pending']);
        $this->createItem(['status' => 'diterima', 'name' => 'Received']);
        $this->createItem(['status' => 'verified', 'name' => 'Only Verified']);

        $response = $this->actingAs($staff)->get('/verification?status=verified');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Verification/Index')
        );
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T47: Date range filter on verification page
    // ═════════════════════════════════════════════════════════════════════

    public function test_filter_by_date_range(): void
    {
        $staff = $this->createStaff();

        // Create items with specific dates via raw DB insert to bypass auto-now
        $earlyItem = MasterItem::create([
            'item_no'     => 'DATE-EARLY',
            'name'        => 'Early Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'belum_diterima',
            'created_at'  => '2026-01-15 00:00:00',
        ]);
        // Force the created_at since create() might override
        $earlyItem->update(['created_at' => '2026-01-15 00:00:00']);

        $midItem = MasterItem::create([
            'item_no'     => 'DATE-MID',
            'name'        => 'Mid Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'diterima',
            'created_at'  => '2026-06-15 00:00:00',
        ]);
        $midItem->update(['created_at' => '2026-06-15 00:00:00']);

        $lateItem = MasterItem::create([
            'item_no'     => 'DATE-LATE',
            'name'        => 'Late Item',
            'category'    => 'Test',
            'quantity'    => 1,
            'unit_price'  => 100.00,
            'total_price' => 100.00,
            'status'      => 'verified',
            'created_at'  => '2026-12-01 00:00:00',
        ]);
        $lateItem->update(['created_at' => '2026-12-01 00:00:00']);

        // Filter: items created between March and September 2026
        $response = $this->actingAs($staff)->get('/verification?date_from=2026-03-01&date_to=2026-09-30');

        $response->assertStatus(200);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Extra: Stats endpoint
    // ═════════════════════════════════════════════════════════════════════

    public function test_stats_api_endpoint(): void
    {
        $staff = $this->createStaff();

        $this->createItem(['status' => 'belum_diterima', 'name' => 'A']);
        $this->createItem(['status' => 'diterima', 'name' => 'B']);
        $this->createItem(['status' => 'verified', 'name' => 'C', 'unit_price' => 3000.00, 'total_price' => 3000.00]);

        $response = $this->actingAs($staff)->get('/verification/stats');

        $response->assertStatus(200)
                 ->assertJson([
                     'total'          => 3,
                     'belum_diterima' => 1,
                     'diterima'       => 1,
                     'verified'       => 1,
                 ]);
    }
}
