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
}
