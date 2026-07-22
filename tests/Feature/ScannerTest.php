<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MasterItem;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScannerTest extends TestCase
{
    use RefreshDatabase;

    private function createStaff(): User
    {
        return User::factory()->staff()->create();
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T32–T35: Scanner Lookup (POST /scanner/lookup)
    // ═════════════════════════════════════════════════════════════════════

    /** T32: Lookup by exact serial_number in master_items */
    public function test_lookup_by_exact_serial_number_finds_match(): void
    {
        $staff = $this->createStaff();
        $item = MasterItem::create([
            'item_no'        => 'ITM-001',
            'name'           => 'Office Desktop',
            'category'       => 'Office',
            'serial_numbers' => 'SN-T32-001',
            'quantity'       => 1,
            'unit_price'     => 5000.00,
            'total_price'    => 5000.00,
            'status'         => 'diterima',
        ]);

        $response = $this->actingAs($staff)
            ->postJson('/scanner/lookup', ['serial_number' => 'SN-T32-001']);

        $response->assertStatus(200)
                 ->assertJson([
                     'serial_number' => 'SN-T32-001',
                     'match_count'   => 1,
                 ]);

        $data = $response->json();
        $this->assertCount(1, $data['matches']);
        $this->assertEquals('Office Desktop', $data['matches'][0]['name']);
        $this->assertEquals('ITM-001', $data['matches'][0]['item_no']);
        $this->assertEquals('diterima', $data['matches'][0]['status']);
        $this->assertNull($data['existing_asset']);
    }

    /** T33: Lookup by partial serial_number (LIKE match) */
    public function test_lookup_by_partial_serial_finds_match(): void
    {
        $staff = $this->createStaff();
        $item = MasterItem::create([
            'item_no'        => 'ITM-002',
            'name'           => 'Network Switch',
            'category'       => 'Network',
            'serial_numbers' => 'FVH30031PAW, FVH30031PNH',
            'quantity'       => 2,
            'unit_price'     => 32249.00,
            'total_price'    => 64498.00,
            'status'         => 'diterima',
        ]);

        // Search by partial serial that appears inside the comma-separated list
        $response = $this->actingAs($staff)
            ->postJson('/scanner/lookup', ['serial_number' => '30031PAW']);

        $response->assertStatus(200)
                 ->assertJsonPath('match_count', 1);

        $data = $response->json();
        $this->assertEquals('Network Switch', $data['matches'][0]['name']);
    }

    /** T34: Lookup by asset_tag (AST-...) finds asset */
    public function test_lookup_by_asset_tag_finds_asset(): void
    {
        $staff = $this->createStaff();
        $masterItem = MasterItem::create([
            'item_no'        => 'ITM-003',
            'name'           => 'Server Rack',
            'category'       => 'Server',
            'serial_numbers' => 'SR-001',
            'quantity'       => 1,
            'unit_price'     => 15000.00,
            'total_price'    => 15000.00,
            'status'         => 'verified',
        ]);
        $asset = Asset::create([
            'master_item_id'   => $masterItem->id,
            'name'             => 'Server Rack',
            'category'         => 'Server',
            'asset_tag'        => 'AST-20260620-000042',
            'serial_number'    => 'SR-001',
            'asset_type'       => 'fixed_asset',
            'purchase_price'   => 15000.00,
            'unit_price'       => 15000.00,
            'quantity'         => 1,
            'location'         => 'Server Room',
            'status'           => 'active',
        ]);

        // Lookup by asset tag
        $response = $this->actingAs($staff)
            ->postJson('/scanner/lookup', ['serial_number' => 'AST-20260620-000042']);

        $response->assertStatus(200);

        $data = $response->json();
        // Should find the asset
        $this->assertNotNull($data['existing_asset']);
        $this->assertEquals('AST-20260620-000042', $data['existing_asset']['asset_tag']);
        $this->assertEquals('Server Rack', $data['existing_asset']['name']);
    }

    /** T35: Lookup by non-existent serial returns empty matches */
    public function test_lookup_by_nonexistent_serial_returns_empty(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)
            ->postJson('/scanner/lookup', ['serial_number' => 'DOES-NOT-EXIST-999']);

        $response->assertStatus(200)
                 ->assertJson([
                     'serial_number' => 'DOES-NOT-EXIST-999',
                     'match_count'   => 0,
                     'existing_asset' => null,
                 ]);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  T36–T39: Record Scan (POST /scanner/scan)
    // ═════════════════════════════════════════════════════════════════════

    /** T36: Record a 'match' scan creates record with all required fields */
    public function test_scan_with_result_match_creates_record(): void
    {
        $staff = $this->createStaff();
        $item = MasterItem::create([
            'item_no'        => 'ITM-004',
            'name'           => 'Monitor 24"',
            'category'       => 'Office',
            'serial_numbers' => 'MN-001',
            'quantity'       => 1,
            'unit_price'     => 513.00,
            'total_price'    => 513.00,
            'status'         => 'diterima',
        ]);

        $response = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number'  => 'MN-001',
                'result'         => 'match',
                'master_item_id' => $item->id,
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('scan.result', 'match')
                 ->assertJsonPath('scan.serial_number', 'MN-001');

        // Verify DB record
        $scan = Scan::where('serial_number', 'MN-001')->first();
        $this->assertNotNull($scan);
        $this->assertEquals('match', $scan->result);
        $this->assertEquals('receive', $scan->scan_type);
        $this->assertEquals($item->id, $scan->master_item_id);
        $this->assertEquals($staff->id, $scan->scanner_user_id);
        $this->assertNotNull($scan->scanned_at);
    }

    /** T37: Record a 'mismatch' scan */
    public function test_scan_with_result_mismatch(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number' => 'UNKNOWN-SN',
                'result'        => 'mismatch',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('scan.result', 'mismatch');

        $scan = Scan::where('serial_number', 'UNKNOWN-SN')->first();
        $this->assertNotNull($scan);
        $this->assertEquals('mismatch', $scan->result);
    }

    /** T38: Record 'new' and 'duplicate' scans */
    public function test_scan_with_new_and_duplicate_results(): void
    {
        $staff = $this->createStaff();

        // 'new' result
        $response1 = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number' => 'BRAND-NEW-SN',
                'result'        => 'new',
            ]);
        $response1->assertStatus(200)
                  ->assertJsonPath('scan.result', 'new');

        // 'duplicate' result
        $response2 = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number' => 'DUP-SN',
                'result'        => 'duplicate',
            ]);
        $response2->assertStatus(200)
                  ->assertJsonPath('scan.result', 'duplicate');

        $this->assertEquals(2, Scan::count());
    }

    /** T39: Scan with all optional fields (location, notes, asset_id, master_item_id) */
    public function test_scan_with_all_optional_fields(): void
    {
        $staff = $this->createStaff();
        $item = MasterItem::create([
            'item_no'        => 'ITM-005',
            'name'           => 'Laptop',
            'category'       => 'Office',
            'serial_numbers' => 'LP-001',
            'quantity'       => 1,
            'unit_price'     => 5000.00,
            'total_price'    => 5000.00,
            'status'         => 'diterima',
        ]);

        $response = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number'  => 'LP-001',
                'result'         => 'match',
                'master_item_id' => $item->id,
                'location'       => 'Lab D',
                'notes'          => 'Found in locker 4B',
            ]);

        $response->assertStatus(200);
        $scan = Scan::where('serial_number', 'LP-001')->first();
        $this->assertEquals('Lab D', $scan->location);
        $this->assertEquals('Found in locker 4B', $scan->notes);
        $this->assertEquals($item->id, $scan->master_item_id);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Validation edge cases
    // ═════════════════════════════════════════════════════════════════════

    /** T40: Scan with invalid result returns validation error */
    public function test_scan_with_invalid_result_returns_error(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)
            ->postJson('/scanner/scan', [
                'serial_number' => 'SN-001',
                'result'        => 'invalid_result_type',
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['result']);
    }

    /** T41: Lookup requires serial_number field */
    public function test_lookup_without_serial_number_returns_error(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)
            ->postJson('/scanner/lookup', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['serial_number']);
    }
}
