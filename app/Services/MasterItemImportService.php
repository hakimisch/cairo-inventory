<?php

namespace App\Services;

use App\Models\MasterItem;
use App\Models\Supplier;

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
     * Import an array of parsed items into master_items.
     *
     * @param array $items Array of item arrays with keys: item_code, category, description,
     *                     brand, serial_number, unit_price, quantity_ordered
     * @param Supplier|null $supplier The supplier to link, or null
     * @return int Number of items imported
     */
    public function importAsMasterItems(array $items, ?Supplier $supplier): int
    {
        $count = 0;
        $existingCount = MasterItem::count();

        foreach ($items as $i => $item) {
            $unitPrice = !empty($item['unit_price']) ? (float) $item['unit_price'] : 0;

            MasterItem::create([
                'item_no'       => $item['item_code'] ?? ($existingCount + $i + 1),
                'category'      => mb_substr($item['category'] ?? 'Uncategorized', 0, 255),
                'name'          => mb_substr($item['description'] ?? '(no name)', 0, 1000),
                'brand'         => isset($item['brand']) ? mb_substr((string) $item['brand'], 0, 255) : null,
                'serial_numbers'=> $item['serial_number'] ?? null,
                'unit_price'    => $unitPrice,
                'total_price'   => $unitPrice * max(1, (int) ($item['quantity_ordered'] ?? 1)),
                'sodo_bayaran'  => null,
                'supplier_id'   => $supplier?->id,
                'quantity'      => (int) ($item['quantity_ordered'] ?? 1),
                'status'        => 'belum_diterima',
            ]);
            $count++;
        }

        return $count;
    }
}
