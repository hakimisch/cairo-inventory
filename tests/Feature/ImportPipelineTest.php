<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\OcrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportPipelineTest extends TestCase
{
    use RefreshDatabase;

    private function createStaff(): User
    {
        return User::factory()->staff()->create();
    }

    // ─── Helpers ────────────────────────────────────────────────────────

    /**
     * POST a CSV file to /import/preview and return the Inertia response.
     */
    private function previewCsv(string $content, string $filename = 'test.csv'): \Illuminate\Testing\TestResponse
    {
        $file = UploadedFile::fake()->createWithContent($filename, $content);

        return $this->actingAs($this->createStaff())
            ->post('/import/preview', [
                'file' => $file,
            ]);
    }

    /**
     * POST a pasted-text string to /import/preview.
     */
    private function previewPasted(string $text): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->createStaff())
            ->post('/import/preview', [
                'pasted' => $text,
            ]);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  2A — CSV Import Tests (T19–T25)
    // ═════════════════════════════════════════════════════════════════════

    /** T19: Import valid CSV creates master_items with belum_diterima status */
    public function test_valid_csv_import_creates_master_items(): void
    {
        $staff = $this->createStaff();

        // Preview step
        $csvContent = "description,quantity,unit_price\nItem A,5,100.00\nItem B,3,200.00";
        $file = UploadedFile::fake()->createWithContent('items.csv', $csvContent);

        $previewResponse = $this->actingAs($staff)->post('/import/preview', [
            'file' => $file,
        ]);

        $previewResponse->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(2, $preview['items']);

        // Confirm step
        $supplier = Supplier::create(['name' => 'Test Supplier', 'is_active' => true]);
        $confirmResponse = $this->actingAs($staff)->post('/import/confirm', [
            'items'       => $preview['items'],
            'supplier_id' => $supplier->id,
        ]);

        $confirmResponse->assertSessionHas('success');

        $items = MasterItem::where('supplier_id', $supplier->id)->get();
        $this->assertCount(2, $items);
        $this->assertEquals('belum_diterima', $items[0]->status);
        $this->assertEquals('Item A', $items[0]->name);
        $this->assertEquals(5, $items[0]->quantity);
        $this->assertEquals(100.00, (float) $items[0]->unit_price);
        $this->assertEquals('Item B', $items[1]->name);
        $this->assertEquals(3, $items[1]->quantity);
        $this->assertEquals(200.00, (float) $items[1]->unit_price);
    }

    /** T20: CSV with Malay headers correctly maps columns */
    public function test_csv_with_malay_headers(): void
    {
        $staff = $this->createStaff();

        $csvContent = "Bil,Nama Item,Kuantiti,Harga Seunit\n1,Monitor 24\",10,513.00\n2,Desktop PC,5,5926.00";
        $file = UploadedFile::fake()->createWithContent('malay.csv', $csvContent);

        $previewResponse = $this->actingAs($staff)->post('/import/preview', [
            'file' => $file,
        ]);

        $previewResponse->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(2, $preview['items']);
        $this->assertEquals('Monitor 24"', $preview['items'][0]['description']);
        $this->assertEquals(10, $preview['items'][0]['quantity_ordered']);
        $this->assertEquals(513.00, $preview['items'][0]['unit_price']);
        $this->assertEquals('Desktop PC', $preview['items'][1]['description']);
        $this->assertEquals(5, $preview['items'][1]['quantity_ordered']);
        $this->assertEquals(5926.00, $preview['items'][1]['unit_price']);
    }

    /** T21: CSV with missing required columns returns graceful error */
    public function test_csv_with_missing_columns_fails_gracefully(): void
    {
        $csvContent = "col_a,col_b\nx,y";
        $file = UploadedFile::fake()->createWithContent('bad.csv', $csvContent);

        $response = $this->actingAs($this->createStaff())
            ->post('/import/preview', [
                'file' => $file,
            ]);

        // The column mapper won't find 'description' or 'item_code',
        // so the parser returns an empty items array
        $response->assertSessionHasErrors('file');
    }

    /** T22: Empty CSV returns error */
    public function test_empty_csv_returns_error(): void
    {
        $csvContent = "header1,header2\n";
        $file = UploadedFile::fake()->createWithContent('empty.csv', $csvContent);

        $response = $this->actingAs($this->createStaff())
            ->post('/import/preview', [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
    }

    /** T23: Import with supplier_id links master_items to supplier */
    public function test_import_with_supplier_id_links_items(): void
    {
        $staff = $this->createStaff();
        $supplier = Supplier::create(['name' => 'Test Supplier Sdn Bhd', 'is_active' => true]);

        $csvContent = "description,quantity,unit_price\nTest Item,1,500.00";
        $file = UploadedFile::fake()->createWithContent('with_supplier.csv', $csvContent);

        $previewResponse = $this->actingAs($staff)->post('/import/preview', [
            'file' => $file,
        ]);
        $previewResponse->assertSessionHas('import_preview');
        $preview = session('import_preview');

        $this->actingAs($staff)->post('/import/confirm', [
            'items'       => $preview['items'],
            'supplier_id' => $supplier->id,
        ])->assertSessionHas('success');

        $item = MasterItem::where('name', 'Test Item')->first();
        $this->assertNotNull($item);
        $this->assertEquals($supplier->id, $item->supplier_id);
    }

    /** T24: Import with supplier_name falls back to firstOrCreate */
    public function test_import_with_supplier_name_auto_creates_supplier(): void
    {
        $staff = $this->createStaff();

        $csvContent = "description,quantity,unit_price\nAuto Supplied Item,2,100.00";
        $file = UploadedFile::fake()->createWithContent('auto_supplier.csv', $csvContent);

        $previewResponse = $this->actingAs($staff)->post('/import/preview', [
            'file' => $file,
        ]);
        $preview = session('import_preview');

        $this->actingAs($staff)->post('/import/confirm', [
            'items'         => $preview['items'],
            'supplier_name' => 'Auto Created Supplier',
        ])->assertSessionHas('success');

        $supplier = Supplier::where('name', 'Auto Created Supplier')->first();
        $this->assertNotNull($supplier);
        $this->assertTrue((bool) $supplier->is_active);

        $item = MasterItem::where('name', 'Auto Supplied Item')->first();
        $this->assertEquals($supplier->id, $item->supplier_id);
    }

    /** T25: Brand-model split heuristic works */
    public function test_brand_model_split_heuristic(): void
    {
        $staff = $this->createStaff();

        // No separate 'model' column — brand field contains "BRAND ModelXYZ"
        $csvContent = "description,brand,quantity,unit_price\nDesktop PC,DELL OptiPlex 7080,3,3500.00";
        $file = UploadedFile::fake()->createWithContent('brand_split.csv', $csvContent);

        $previewResponse = $this->actingAs($staff)->post('/import/preview', [
            'file' => $file,
        ]);

        $previewResponse->assertSessionHas('import_preview');
        $preview = session('import_preview');

        $this->assertCount(1, $preview['items']);
        $this->assertEquals('DELL', $preview['items'][0]['brand']);
        $this->assertEquals('OptiPlex 7080', $preview['items'][0]['model']);

        // Confirm and check saved correctly
        $supplier = Supplier::create(['name' => 'Brand Test Supplier', 'is_active' => true]);
        $this->actingAs($staff)->post('/import/confirm', [
            'items'       => $preview['items'],
            'supplier_id' => $supplier->id,
        ])->assertSessionHas('success');

        $item = MasterItem::where('name', 'Desktop PC')->first();
        $this->assertEquals('DELL', $item->brand);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  2B — OCR Pipeline Tests (T26–T28) — OcrService mocked
    // ═════════════════════════════════════════════════════════════════════

    /** T26: OCR pipeline returns valid item data via mock */
    public function test_ocr_pipeline_returns_items(): void
    {
        $staff = $this->createStaff();

        $this->mock(OcrService::class, function ($mock) {
            $mock->shouldReceive('runOcr')
                ->once()
                ->andReturn(json_encode([
                    'items' => [
                        [
                            'item_code'        => '1.1',
                            'description'      => 'Office Desktop',
                            'brand'            => 'DELL',
                            'model'            => 'OptiPlex',
                            'quantity_ordered' => 14,
                            'unit'             => 'units',
                            'unit_price'       => 5926.00,
                            'total_price'      => 82964.00,
                            'category'         => 'OFFICE EQUIPMENTS',
                        ],
                        [
                            'item_code'        => '1.2',
                            'description'      => 'Standard Monitor',
                            'quantity_ordered' => 14,
                            'unit_price'       => 513.00,
                        ],
                    ],
                    'reference'      => 'UTMCARD/2025/005',
                    'supplier'       => 'DOTCOM Telecommunications',
                    'date'           => '12/03/2026',
                    'total_amount'   => 2582970.00,
                    'document_type'  => 'po',
                    'raw_text'       => 'OCR extracted raw text...',
                    'confidence'     => 0.87,
                ]));
        });

        // Send a fake PDF file to trigger the OCR path
        $fakePdf = UploadedFile::fake()->create('document.pdf', 1024);

        $response = $this->actingAs($staff)->post('/import/preview', [
            'file'      => $fakePdf,
            'type_hint' => 'po',
        ]);

        $response->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(2, $preview['items']);
        $this->assertEquals('Office Desktop', $preview['items'][0]['description']);
        $this->assertEquals(5926.00, $preview['items'][0]['unit_price']);
    }

    /** T27: OCR extracts document reference and metadata */
    public function test_ocr_extracts_metadata(): void
    {
        $staff = $this->createStaff();

        $this->mock(OcrService::class, function ($mock) {
            $mock->shouldReceive('runOcr')
                ->once()
                ->andReturn(json_encode([
                    'items' => [
                        ['description' => 'Test Item', 'quantity_ordered' => 1, 'unit_price' => 100.00],
                    ],
                    'reference'    => 'PO-2025-001',
                    'supplier'     => 'Supplier A',
                    'date'         => '01/01/2025',
                    'total_amount' => 100.00,
                    'document_type' => 'po',
                ]));
        });

        $fakePdf = UploadedFile::fake()->create('meta.pdf', 512);

        $response = $this->actingAs($staff)->post('/import/preview', [
            'file' => $fakePdf,
        ]);

        $preview = session('import_preview');
        $this->assertEquals('PO-2025-001', $preview['metadata']['reference']);
        $this->assertEquals('Supplier A', $preview['metadata']['supplier']);
        $this->assertEquals('po', $preview['metadata']['type']);
    }

    /** T28: OCR type_hint is passed through to service */
    public function test_ocr_type_hint_passed_to_service(): void
    {
        $staff = $this->createStaff();

        $this->mock(OcrService::class, function ($mock) {
            $mock->shouldReceive('runOcr')
                ->once()
                ->with(\Mockery::type('string'), 'quotation')
                ->andReturn(json_encode([
                    'items' => [
                        ['description' => 'Quoted Item', 'quantity_ordered' => 1, 'unit_price' => 500.00],
                    ],
                    'document_type' => 'quotation',
                ]));
        });

        $fakePdf = UploadedFile::fake()->create('quote.pdf', 512);

        $response = $this->actingAs($staff)->post('/import/preview', [
            'file'      => $fakePdf,
            'type_hint' => 'quotation',
        ]);

        $preview = session('import_preview');
        $this->assertCount(1, $preview['items']);
        $this->assertEquals('quotation', $preview['metadata']['type']);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  2C — Pasted Text & Excel Tests (T29–T31)
    // ═════════════════════════════════════════════════════════════════════

    /** T29: Pasted CSV (comma-separated) parses correctly */
    public function test_pasted_csv_is_parsed(): void
    {
        $pastedContent = "description,quantity,unit_price\nItem A,5,100.00\nItem B,3,200.00";
        $response = $this->previewPasted($pastedContent);

        $response->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(2, $preview['items']);
        $this->assertEquals('Item A', $preview['items'][0]['description']);
        $this->assertEquals(5, $preview['items'][0]['quantity_ordered']);
    }

    /** T30: Pasted single-column text falls back to one-item-per-line */
    public function test_pasted_single_column_becomes_single_items(): void
    {
        $pastedContent = "Office Desktop\nStandard Monitor\nAccess Point";
        $response = $this->previewPasted($pastedContent);

        $response->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(3, $preview['items']);
        $this->assertEquals('Office Desktop', $preview['items'][0]['description']);
        $this->assertEquals(1, $preview['items'][0]['quantity_ordered']);
        $this->assertEquals('Standard Monitor', $preview['items'][1]['description']);
        $this->assertEquals('Access Point', $preview['items'][2]['description']);
    }

    /** T31: Excel upload falls back to CSV parser when PhpSpreadsheet absent */
    public function test_excel_upload_falls_back_to_csv(): void
    {
        $staff = $this->createStaff();

        // Even without PhpSpreadsheet, the controller should fall back
        // to parseCsv which treats the .xlsx as a plain text file.
        // This validates the graceful degradation path.
        $csvLikeContent = "description,quantity,unit_price\nExcel Item,10,500.00";
        $fakeExcel = UploadedFile::fake()->createWithContent('data.xlsx', $csvLikeContent);

        $response = $this->actingAs($staff)->post('/import/preview', [
            'file' => $fakeExcel,
        ]);

        $response->assertSessionHas('import_preview');
        $preview = session('import_preview');
        $this->assertCount(1, $preview['items']);
        $this->assertEquals('Excel Item', $preview['items'][0]['description']);
    }
}
