<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;
use App\Models\Receiving;
use App\Models\AssetPlacement;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Wipe old data to prevent duplicates
        Schema::disableForeignKeyConstraints();
        AssetPlacement::truncate();
        Asset::truncate();
        Receiving::truncate();
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        // ==========================================
        // 2. SEED RECEIVINGS (For ReceivingIndex.jsx)
        // ==========================================
        
        // Accepted Items (Will show as "Diterima" in your UI)
        $rec1 = Receiving::create([
            'receive_no'        => 'RC-20250109-001',
            'supplier_name'     => 'UNI-TECHNOLOGIES SDN. BHD.',
            'supplier_address'  => 'LEVEL 2, INDUSTRY BUILDING TECHNOVATION PARK, UTM SKUDAI',
            'purchase_order_no' => 'PPTK090302112024000270',
            'delivery_order_no' => 'DO24/61',
            'invoice_no'        => 'INV-2025-0012',
            'item_description'  => 'CAMERA TRAP HC-900 LTE',
            'quantity_ordered'  => 20,
            'quantity_received' => 20,
            'unit_price'        => 948.75,
            'total_price'       => 18975.00,
            'status'            => 'accepted',
            'receiver_name'     => 'NORHAYATI BINTI KAMALUDIN',
            'receiver_position' => 'PEMBANTU TADBIR',
            'created_at'        => $now->copy()->subDays(30),
        ]);

        // Pending Items (Will show "Terima & Daftar" buttons in your UI)
        $pendingReceivings = [
            [
                'receive_no'        => 'RC-' . $now->format('Ymd') . '-002',
                'supplier_name'     => 'NVIDIA MALAYSIA S/B',
                'supplier_address'  => 'Level 15, Menara Binjai, Kuala Lumpur',
                'purchase_order_no' => 'PPTK-AI-2026-0045',
                'delivery_order_no' => 'DO-NV-8832',
                'invoice_no'        => 'INV-NV-991',
                'item_description'  => 'NVIDIA RTX 6090 GPU NODE',
                'quantity_ordered'  => 4,
                'quantity_received' => 4,
                'unit_price'        => 15500.00,
                'total_price'       => 62000.00,
                'status'            => 'pending',
            ],
            [
                'receive_no'        => 'RC-' . $now->format('Ymd') . '-003',
                'supplier_name'     => 'DELL TECHNOLOGIES MALAYSIA',
                'supplier_address'  => 'Plot G, Bayan Lepas, Penang',
                'purchase_order_no' => 'PO-DELL-2026-012',
                'delivery_order_no' => 'DO-DL-1002',
                'invoice_no'        => 'INV-DL-445',
                'item_description'  => 'DELL POWEREDGE R750 SERVER',
                'quantity_ordered'  => 2,
                'quantity_received' => 2,
                'unit_price'        => 24500.00,
                'total_price'       => 49000.00,
                'status'            => 'pending',
            ],
            [
                'receive_no'        => 'RC-' . $now->format('Ymd') . '-004',
                'supplier_name'     => 'MALAYSIA SENSOR SOLUTIONS',
                'supplier_address'  => 'Shah Alam, Selangor',
                'purchase_order_no' => 'PO-SEN-8871',
                'delivery_order_no' => 'DO-SEN-221',
                'invoice_no'        => 'INV-SEN-009',
                'item_description'  => 'IOT AIR QUALITY SENSOR NODES',
                'quantity_ordered'  => 15,
                'quantity_received' => 15,
                'unit_price'        => 350.00,
                'total_price'       => 5250.00,
                'status'            => 'pending',
            ],
        ];

        foreach ($pendingReceivings as $rec) {
            Receiving::create(array_merge($rec, [
                'receiver_name'     => 'Hakimi', // Using your nickname from context
                'receiver_position' => 'INTERN DEVELOPER',
                'created_at'        => $now,
            ]));
        }

        // ==========================================
        // 3. SEED REGISTERED ASSETS (KEW.PA-2 & 3)
        // ==========================================

        $assetsData = [
            [
                'asset_tag' => 'HT-0000197/2025',
                'name' => 'HIGH-END PERFORMANCE LAPTOP',
                'category' => 'Workstation',
                'asset_type' => 'fixed_asset',
                'campus' => 'utm_jb',
                'purchase_price' => 11117.00,
                'location' => 'FK - MAKMAL ACTUATOR',
                'model' => 'LEGION PRO 5I',
                'brand' => 'LENOVO',
                'serial_number' => 'PF57G36K',
                'custodian_name' => 'Ts. Dr. Mohd Ibrahim',
            ],
            [
                'asset_tag' => 'IN-0000251/2025',
                'name' => 'CAMERA TRAP',
                'category' => 'Sensor',
                'asset_type' => 'inventory',
                'campus' => 'utm_jb',
                'purchase_price' => 948.75,
                'location' => 'MAKMAL ECOLOGY',
                'model' => 'HC-900 LTE',
                'brand' => 'TRAIL CAMERA',
                'serial_number' => '2024/08/28/900/00110',
                'custodian_name' => 'Prof. Madya Dr. Salina',
            ],
        ];

        foreach ($assetsData as $data) {
            $asset = Asset::create(array_merge($data, [
                'status' => 'active',
                'supplier_name' => 'UNI-TECHNOLOGIES SDN. BHD.',
                'received_date' => $now->copy()->subDays(60),
                'saga_id' => 'B' . rand(30000, 40000),
                'budget_vot' => 'A.K090302.5500.07204',
                'po_reference' => 'PPTK090302112024000270',
                'do_reference' => 'DO24/61',
            ]));

            // 4. Initial Placement
            AssetPlacement::create([
                'asset_id'       => $asset->id,
                'custodian_name' => $asset->custodian_name,
                'location'       => $asset->location,
                'is_lokasi_luar' => false,
                'assigned_date'  => $asset->received_date,
            ]);
        }
    }
}