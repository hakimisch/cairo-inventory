<?php

namespace Database\Seeders;

use App\Models\MasterItem;
use App\Models\Receiving;
use App\Models\Asset;
use App\Models\AssetPlacement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardTestSeeder extends Seeder
{
    /**
     * Seed test data for the admin dashboard.
     *
     * Creates verified Assets, Receivings (PA-1), and Placements
     * from the first 10 "diterima" master items.
     *
     * Usage:
     *   php artisan db:seed --class=DashboardTestSeeder
     *
     * Cleanup via tinker:
     *   \App\Models\Asset::where('asset_tag', 'LIKE', 'AST-TEST-%')->delete();
     *   \App\Models\Receiving::where('receive_no', 'LIKE', 'REC-TEST-%')->delete();
     *   \App\Models\MasterItem::where('status','verified')->update(['status'=>'diterima','verified_at'=>null,'verified_by'=>null,'asset_type'=>null]);
     */
    public function run(): void
    {
        $userId = 1;
        $threshold = 2000;

        $items = MasterItem::orderBy('item_no')->get();

        if ($items->isEmpty()) {
            $this->command->warn('No master items found. Run MasterItemSeeder first.');
            return;
        }

        $this->command->info('Creating test data from ' . $items->count() . ' master items...');
        $bar = $this->command->getOutput()->createProgressBar($items->count());
        $bar->start();

        foreach ($items as $masterItem) {
            DB::transaction(function () use ($masterItem, $userId, $threshold) {
                $masterItem->update([
                    'status'      => 'verified',
                    'verified_at' => now()->subDays(rand(1, 30)),
                    'verified_by' => $userId,
                    'asset_type'  => $masterItem->unit_price >= $threshold ? 'fixed_asset' : 'inventory',
                ]);

                $receiveNo = 'REC-TEST-' . str_pad($masterItem->id, 4, '0', STR_PAD_LEFT);
                Receiving::create([
                    'master_item_id'    => $masterItem->id,
                    'receive_no'        => $receiveNo,
                    'supplier_name'     => $masterItem->supplier?->name ?? 'Test Supplier',
                    'supplier_address'  => 'Test Address',
                    'delivery_order_no' => $masterItem->do_no ?? 'N/A',
                    'purchase_order_no' => 'N/A',
                    'invoice_no'        => 'INV-TEST-' . str_pad($masterItem->id, 4, '0', STR_PAD_LEFT),
                    'item_description'  => $masterItem->name,
                    'quantity_ordered'  => $masterItem->quantity,
                    'quantity_received' => $masterItem->quantity,
                    'unit_price'        => $masterItem->unit_price,
                    'total_price'       => $masterItem->total_price,
                    'status'            => 'accepted',
                    'receiver_name'     => 'Super Admin',
                    'receiver_position' => 'Pentadbir Sistem',
                ]);

                $sn = $masterItem->serial_numbers;
                if ($sn && strlen($sn) > 250) {
                    $sn = substr($sn, 0, 250) . '...';
                }

                $asset = Asset::create([
                    'master_item_id'   => $masterItem->id,
                    'asset_tag'        => 'AST-TEST-' . str_pad($masterItem->id, 4, '0', STR_PAD_LEFT),
                    'name'             => $masterItem->name,
                    'category'         => $masterItem->category,
                    'brand'            => $masterItem->brand,
                    'serial_number'    => $sn,
                    'asset_type'       => $masterItem->asset_type,
                    'campus'           => 'utm_kl',
                    'purchase_price'   => $masterItem->unit_price ?: 1000,
                    'unit_price'       => $masterItem->unit_price,
                    'quantity'         => $masterItem->quantity,
                    'unit_of_measure'  => 'Unit',
                    'location'         => 'Makmal AI',
                    'status'           => 'active',
                    'supplier_name'    => $masterItem->supplier?->name ?? 'Test Supplier',
                    'received_date'    => now()->subDays(rand(1, 30)),
                    'verified_by_scan' => true,
                    'scan_verified_at' => now(),
                ]);

                // Add placement for a few items
                if ($masterItem->id % 3 === 0) {
                    AssetPlacement::create([
                        'asset_id'       => $asset->id,
                        'custodian_name' => 'Ts. Dr. Mohd Ibrahim',
                        'location'       => 'Makmal AI',
                        'is_lokasi_luar' => false,
                        'assigned_date'  => now()->subDays(rand(5, 20)),
                    ]);
                }
            });
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);

        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Master items verified', $items->count()],
                ['Receivings (PA-1)', Receiving::count()],
                ['Assets created', Asset::count()],
                ['Placements', AssetPlacement::count()],
            ]
        );
    }
}
