<?php

namespace App\Services;

use App\Models\MasterItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * Shared service for importing parsed items into the master_items register.
 *
 * Replaces duplicate importAsMasterItems() in:
 *   - ImportController
 *   - PdfImportController
 *   - ImportPdfData (CLI command)
 */
class MasterItemImportService
{
    /**
     * Import items into master_items inside a single DB transaction, skipping
     * duplicates (normalized item_no + description match).
     *
     * @param array $items Array of item arrays with keys: item_code, category, description,
     *                     brand, serial_number, specification, unit_price, total_price,
     *                     quantity_ordered, country_of_origin, sodo_bayaran, do_no,
     *                     invoice_no, received_date, supplier_name
     * @param Supplier|null $supplier The supplier to link, or null
     * @return array{imported: int, skipped: int} Counts of created and skipped items
     */
    public function importAsMasterItems(array $items, ?Supplier $supplier): array
    {
        $imported = 0;
        $skipped = 0;
        $existingCount = MasterItem::count();

        DB::transaction(function () use ($items, $supplier, &$imported, &$skipped, $existingCount) {
            foreach ($items as $i => $item) {
                $unitPrice = !empty($item['unit_price']) ? (float) $item['unit_price'] : 0;
                $qty = max(1, (int) ($item['quantity_ordered'] ?? 1));

                // ── Duplicate guard: same item_no + normalized description ──
                $itemCode = $item['item_code'] ?? null;
                $description = (string) ($item['description'] ?? '(no name)');
                if ($itemCode !== null && trim((string) $itemCode) !== '') {
                    $dup = MasterItem::where('item_no', $itemCode)
                        ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($description))])
                        ->exists();

                    if ($dup) {
                        $skipped++;
                        continue;
                    }
                }

                // Resolve supplier from item-level supplier_name if no global supplier set
                $itemSupplier = $supplier;
                if (!$itemSupplier && !empty($item['supplier_name'])) {
                    $itemSupplier = Supplier::firstOrCreate(
                        ['name' => trim($item['supplier_name'])],
                        ['is_active' => true]
                    );
                }

                // Parse total_price: prefer parsed value, fall back to unit_price × qty
                $totalPrice = !empty($item['total_price']) ? (float) $item['total_price'] : $unitPrice * $qty;

                // Parse received_date if provided
                $receivedDate = null;
                if (!empty($item['received_date'])) {
                    try {
                        $receivedDate = \Carbon\Carbon::parse($item['received_date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $receivedDate = null;
                    }
                }

                MasterItem::create([
                    'item_no'           => $itemCode ?? ($existingCount + $i + 1),
                    'category'          => mb_substr($item['category'] ?? 'Uncategorized', 0, 255),
                    'name'              => mb_substr($description, 0, 1000),
                    'specification'     => isset($item['specification']) ? mb_substr((string) $item['specification'], 0, 1000) : null,
                    'brand'             => isset($item['brand']) ? mb_substr((string) $item['brand'], 0, 255) : null,
                    'serial_numbers'    => $item['serial_number'] ?? null,
                    'country_of_origin' => isset($item['country_of_origin']) ? mb_substr((string) $item['country_of_origin'], 0, 255) : null,
                    'unit_price'        => $unitPrice,
                    'total_price'       => $totalPrice,
                    'sodo_bayaran'      => isset($item['sodo_bayaran']) ? mb_substr((string) $item['sodo_bayaran'], 0, 255) : null,
                    'supplier_id'       => $itemSupplier?->id,
                    'quantity'          => $qty,
                    'status'            => 'belum_diterima',
                    'do_no'             => $item['do_no'] ?? null,
                    'invoice_no'        => $item['invoice_no'] ?? null,
                    'received_date'     => $receivedDate,
                ]);
                $imported++;
            }
        });

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
