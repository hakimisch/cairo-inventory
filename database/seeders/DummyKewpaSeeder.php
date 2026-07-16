<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetPlacement;
use App\Models\DamageReport;
use App\Models\MasterItem;
use App\Models\Receiving;
use Illuminate\Database\Seeder;

class DummyKewpaSeeder extends Seeder
{
    /**
     * Seed 20 dummy records for kewpa forms and dashboards.
     *
     * Run: php artisan db:seed --class=DummyKewpaSeeder
     */
    public function run(): void
    {
        // ── Pick 20 "diterima" master items to work with ───────────────────
        $pool = MasterItem::where('status', 'diterima')->get();
        if ($pool->isEmpty()) {
            $this->command->warn('No diterima master items found. Run MasterItemSeeder first.');
            return;
        }

        // ── 1. Receivings (PA-1) ──────────────────────────────────────────
        $receivingItems = $pool->shuffle()->take(6);
        $receivings = [];
        $receiveNo = 1;

        foreach ($receivingItems as $mi) {
            $qty = max(1, $mi->quantity ?? 1);
            $unitPrice = $mi->unit_price ?: rand(500, 50000);
            $totalPrice = $unitPrice * $qty;

            $status = $receiveNo <= 4 ? 'accepted' : 'pending';

            $rec = Receiving::create([
                'master_item_id'           => $mi->id,
                'receive_no'               => 'RCP-' . now()->format('Ymd') . '-' . str_pad($receiveNo, 3, '0', STR_PAD_LEFT),
                'supplier_name'            => $mi->supplier?->name ?? 'Unknown Supplier',
                'supplier_address'         => 'No. ' . rand(1, 100) . ', Jalan Industri, Kawasan Perindustrian, 81300 Johor Bahru',
                'purchase_order_no'        => 'PO/CAIRO/' . now()->year . '/' . str_pad(rand(1, 50), 4, '0', STR_PAD_LEFT),
                'delivery_order_no'        => $mi->do_no ?? 'DO/CAIRO/' . now()->year . '/' . str_pad(rand(1, 50), 4, '0', STR_PAD_LEFT),
                'invoice_no'               => 'INV/' . now()->year . '/' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                'item_description'         => $mi->name,
                'quantity_ordered'         => $qty,
                'quantity_received'        => $status === 'accepted' ? $qty : rand(1, $qty),
                'unit_price'               => $unitPrice,
                'total_price'              => $totalPrice,
                'status'                   => $status,
                'receiver_name'            => $this->randomName(),
                'receiver_position'        => 'Pegawai Penerima',
                'technical_officer_name'   => $this->randomName(),
                'technical_officer_position' => 'Pegawai Bertanggungjawab',
                'damage_description'       => $status === 'accepted' ? null : 'Minor cosmetic damage on outer packaging',
                'notes'                    => $status === 'accepted' ? 'Item received in good condition' : 'Pending inspection',
            ]);

            $receivings[] = $rec;
            $receiveNo++;
        }

        $this->command->info('Created ' . count($receivings) . ' receivings (PA-1).');

        // ── 2. Assets (KEW.PA-2 / KEW.PA-3) ───────────────────────────────
        $assetPool = $pool->shuffle()->take(10);
        $assetTag = 1;
        $assets = [];

        $custodians = [
            'Ts. Dr. Ahmad Faizal bin Mohd Zain',
            'Dr. Nurul Huda binti Abdullah',
            'Prof. Madya Ir. Mohd Kamal bin Ariffin',
            'Dr. Siti Fatimah binti Ismail',
            'Ts. Mohd Hafiz bin Ramli',
            'Dr. Lim Wei Sheng',
            'Prof. Dr. Karthigesu a/l T. Murugan',
            'Ts. Nur Amirah binti Hassan',
        ];

        $campuses = ['utm_kl', 'utm_jb'];
        $statuses = ['active', 'active', 'active', 'active', 'repair', 'active', 'active', 'active', 'active', 'disposed'];
        $locations = [
            'Makmal Kejuruteraan, Aras 2',
            'Bilik Pensyarah, Aras 3',
            'Makmal Penyelidikan, Aras G',
            'Bilik Kuliah 4, Aras 1',
            'Makmal Komputer, Aras 4',
            'Pusat Penyelidikan, Aras 2',
            'Makmal Robotik, Aras G',
            'Bilik Mesyuarat Utama, Aras 5',
            'Makmal Bioteknologi, Aras 3',
            'Bilik Stor, Aras G',
        ];

        foreach ($assetPool as $i => $mi) {
            $price = $mi->unit_price ?: rand(800, 80000);
            $type = $price > 1000 ? 'fixed_asset' : 'inventory';
            $campus = $campuses[array_rand($campuses)];
            $status = $statuses[$i] ?? 'active';
            $tag = 'PA' . ($type === 'fixed_asset' ? '2' : '3') . '/' . now()->format('y') . '/' . str_pad($assetTag, 4, '0', STR_PAD_LEFT);

            // Pick a custodian (rotating)
            $custodian = $custodians[$i % count($custodians)];

            $asset = Asset::create([
                'asset_tag'            => $tag,
                'name'                 => $mi->name,
                'category'             => $mi->category,
                'sub_category'         => null,
                'asset_type'           => $type,
                'campus'               => $campus,
                'purchase_price'       => $price,
                'location'             => $locations[$i],
                'status'               => $status,
                'quantity'             => max(1, $mi->quantity ?? 1),
                'unit_of_measure'      => 'unit',
                'supplier_name'        => $mi->supplier?->name ?? 'Unknown',
                'po_reference'         => 'PO/CAIRO/' . now()->year . '/' . str_pad(rand(1, 50), 4, '0', STR_PAD_LEFT),
                'warranty_period'      => rand(1, 5) . ' years',
                'warranty_expiry'      => now()->addYears(rand(1, 5)),
                'received_date'        => now()->subDays(rand(30, 180)),
                'receiver_name'        => $this->randomName(),
                'custodian_name'       => $status !== 'disposed' ? $custodian : null,
                'model'                => $mi->brand ? ($mi->brand . ' ' . chr(rand(65, 90)) . rand(100, 999)) : null,
                'brand'                => $mi->brand ?: null,
                'serial_number'        => $mi->serial_numbers ? explode(',', $mi->serial_numbers)[0] : null,
                'requires_maintenance' => rand(0, 3) === 0,
                'master_item_id'       => $mi->id,
                'unit_price'           => $price,
            ]);

            $assets[] = $asset;
            $assetTag++;
        }

        $this->command->info('Created ' . count($assets) . ' assets (PA-2/PA-3).');

        // ── 3. Asset Placements (KEW.PA-9A) ────────────────────────────────
        $placementCount = 0;
        $placedAssets = collect($assets)->take(4);
        foreach ($placedAssets as $asset) {
            if ($asset->status === 'disposed') continue;

            AssetPlacement::create([
                'asset_id'      => $asset->id,
                'location'      => $locations[array_rand($locations)],
                'assigned_date' => now()->subDays(rand(1, 60)),
                'custodian_name' => $asset->custodian_name,
                'quantity_placed' => 1,
            ]);
            $placementCount++;
        }

        $this->command->info("Created {$placementCount} asset placements (PA-9A).");

        // ── 4. Damage Reports ──────────────────────────────────────────────
        $damageCount = 0;
        foreach (collect($assets)->shuffle()->take(2) as $asset) {
            DamageReport::create([
                'asset_id'           => $asset->id,
                'reported_by'        => $this->randomName(),
                'damage_date'        => now()->subDays(rand(1, 30)),
                'damage_description' => 'Screen has minor cracks on the top left corner. Unit still operational.',
                'status'             => 'pending',
            ]);
            $damageCount++;
        }

        $this->command->info("Created {$damageCount} damage reports.");

        // ── Summary ────────────────────────────────────────────────────────
        $total = count($receivings) + count($assets) + $placementCount + $damageCount;
        $this->command->info("✅ Seeded {$total} total dummy records for kewpa forms & dashboards.");
    }

    private function randomName(): string
    {
        $first = ['Ahmad', 'Mohd', 'Nurul', 'Siti', 'Muhammad', 'Fatimah', 'Hafiz', 'Aminah', 'Zainal', 'Roshidah'];
        $last  = ['bin Abdullah', 'bin Ramli', 'binti Hassan', 'binti Ismail', 'bin Mohd Noor', 'binti Ahmad', 'bin Yusof', 'bin Kassim', 'binti Omar', 'bin Harun'];

        return $first[array_rand($first)] . ' ' . $last[array_rand($last)];
    }
}
