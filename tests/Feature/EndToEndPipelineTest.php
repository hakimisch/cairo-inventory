<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MasterItem;
use App\Models\Receiving;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EndToEndPipelineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Full end-to-end smoke test: Import → Preview → Confirm → Receive → Verify → PA-2
     *
     * Validates the entire data pipeline:
     *    CSV upload → master_items  → receive (PA-1) → verify (PA-2/PA-3) → asset created
     */
    public function test_full_import_to_asset_pipeline(): void
    {
        // ── 1. Setup ────────────────────────────────────────────────────────
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::create(['name' => 'DOTCOM Telecommunications Sdn Bhd', 'is_active' => true]);

        // ── 2. Import step: Upload CSV → preview → confirm ─────────────────
        $csvContent = "item_code,description,brand,quantity,unit_price,category\n"
            . "2.1.1.1,Office Desktop,DELL,14,5926.00,OFFICE EQUIPMENTS\n"
            . "2.1.1.2,Standard Monitor,DELL,14,513.00,OFFICE EQUIPMENTS\n"
            . "2.1.2.1,Access Points,RUIJIE,11,1631.00,NETWORK\n";

        $file = UploadedFile::fake()->createWithContent('equipment.csv', $csvContent);

        // Preview
        $previewResponse = $this->actingAs($admin)->post('/import/preview', [
            'file' => $file,
        ]);
        $previewResponse->assertSessionHas('import_preview');
        $preview = session('import_preview');

        $this->assertCount(3, $preview['items']);
        $this->assertEquals('Office Desktop', $preview['items'][0]['description']);
        $this->assertEquals(5926.00, $preview['items'][0]['unit_price']);

        // Confirm
        $confirmResponse = $this->actingAs($admin)->post('/import/confirm', [
            'items'       => $preview['items'],
            'supplier_id' => $supplier->id,
        ]);
        $confirmResponse->assertSessionHas('success');

        // ── 3. Verify master_items created correctly ────────────────────────
        $this->assertEquals(3, MasterItem::count());

        $desktop = MasterItem::where('name', 'Office Desktop')->first();
        $this->assertNotNull($desktop);
        $this->assertEquals('belum_diterima', $desktop->status);
        $this->assertEquals(14, $desktop->quantity);
        $this->assertEquals(5926.00, (float) $desktop->unit_price);
        $this->assertEquals($supplier->id, $desktop->supplier_id);

        $monitor = MasterItem::where('name', 'Standard Monitor')->first();
        $this->assertNotNull($monitor);
        $this->assertEquals('belum_diterima', $monitor->status);

        $ap = MasterItem::where('name', 'Access Points')->first();
        $this->assertNotNull($ap);
        $this->assertEquals(11, $ap->quantity);
        $this->assertEquals(1631.00, (float) $ap->unit_price);

        // ── 4. Receive step: belum_diterima → diterima (PA-1 created) ──────
        $receiveResponse = $this->actingAs($admin)->post("/verification/{$desktop->id}/receive", [
            'do_no'             => 'DO-2025-001',
            'receiver_name'     => 'Ahmad Bin Ali',
            'receiver_position' => 'Technical Officer',
            'invoice_no'        => 'INV-001',
        ]);
        $receiveResponse->assertSessionHas('success');

        $desktop->refresh();
        $this->assertEquals('diterima', $desktop->status);
        $this->assertNotNull($desktop->received_date);

        // PA-1 Receiving record created
        $receiving = Receiving::where('master_item_id', $desktop->id)->first();
        $this->assertNotNull($receiving);
        $this->assertEquals('DO-2025-001', $receiving->delivery_order_no);
        $this->assertEquals('Ahmad Bin Ali', $receiving->receiver_name);
        $this->assertEquals(5926.00, (float) $receiving->unit_price);
        $this->assertEquals(14 * 5926.00, (float) $receiving->total_price);
        $this->assertEquals('Office Desktop', $receiving->item_description);

        // ── 5. Verify step: diterima → verified (PA-2 or PA-3 asset created) ─
        // Desktop = RM 5,926 >= RM 2,000 → PA-2 (fixed_asset)
        $verifyResponse = $this->actingAs($admin)->post("/verification/{$desktop->id}/verify", [
            'location'       => 'CAIRO Lab H4',
            'custodian_name' => 'Dr. Amin',
        ]);
        $verifyResponse->assertSessionHas('success');

        $desktop->refresh();
        $this->assertEquals('verified', $desktop->status);
        $this->assertNotNull($desktop->verified_at);
        $this->assertEquals($admin->id, $desktop->verified_by);

        // Asset created (PA-2 — fixed_asset, price >= 2000)
        $asset = Asset::where('master_item_id', $desktop->id)->first();
        $this->assertNotNull($asset);
        $this->assertEquals('fixed_asset', $asset->asset_type);
        $this->assertEquals('CAIRO Lab H4', $asset->location);
        $this->assertEquals('Dr. Amin', $asset->custodian_name);
        $this->assertEquals('DELL', $asset->brand);
        $this->assertEquals('Office Desktop', $asset->name);
        $this->assertEquals('DOTCOM Telecommunications Sdn Bhd', $asset->supplier_name);
        $this->assertMatchesRegularExpression('/^AST-\d{8}-\d{6}$/', $asset->asset_tag);
        $this->assertEquals('active', $asset->status);

        // ── 6. Verify 2nd item (inventory — PA-3, price < 2000) ────────────
        // Monitor = RM 513 < RM 2,000 → PA-3 (inventory)
        $receiveMonitor = $this->actingAs($admin)->post("/verification/{$monitor->id}/receive", [
            'do_no' => 'DO-2025-002',
        ]);
        $receiveMonitor->assertSessionHas('success');

        $verifyMonitor = $this->actingAs($admin)->post("/verification/{$monitor->id}/verify", [
            'location' => 'CAIRO Lab H4',
        ]);
        $verifyMonitor->assertSessionHas('success');

        $monitorAsset = Asset::where('master_item_id', $monitor->id)->first();
        $this->assertNotNull($monitorAsset);
        $this->assertEquals('inventory', $monitorAsset->asset_type);
        $this->assertEquals(14, $monitorAsset->quantity);

        // ── 7. Verify stats endpoint ────────────────────────────────────────
        $statsResponse = $this->actingAs($admin)->get('/verification/stats');
        $statsResponse->assertStatus(200)
                      ->assertJson([
                          'total'          => 3,
                          'belum_diterima' => 1,   // Access Points still pending
                          'diterima'       => 0,
                          'verified'       => 2,   // Desktop + Monitor verified
                          'with_asset'     => 2,
                      ]);

        // ── 8. Verify PA-1 and PA-2 pages load (Inertia) ───────────────────
        $pa1Response = $this->actingAs($admin)
            ->get("/receivings/{$receiving->id}/kewpa1");
        $pa1Response->assertStatus(200);
        $pa1Response->assertInertia(fn ($page) => $page
            ->component('Assets/Kewpa1')
            ->where('receiving.receive_no', $receiving->receive_no)
        );

        $pa2Response = $this->actingAs($admin)
            ->get("/assets/{$asset->id}/kewpa2");
        $pa2Response->assertStatus(200);
        $pa2Response->assertInertia(fn ($page) => $page
            ->component('Assets/Kewpa2')
            ->where('asset.asset_tag', $asset->asset_tag)
            ->has('asset.upgrades')   // PA-2 has Section B
        );

        // ── 9. Verify scanner lookup finds imported items ──────────────────
        $scanResponse = $this->actingAs($admin)
            ->postJson('/scanner/lookup', ['serial_number' => 'Office Desktop']);
        $scanResponse->assertStatus(200)
                     ->assertJsonPath('serial_number', 'Office Desktop');
    }
}
