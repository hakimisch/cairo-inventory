<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;
use App\Models\Receiving;
use App\Models\AssetPlacement;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Safely wipe all old data
        Schema::disableForeignKeyConstraints();
        AssetPlacement::truncate();
        Asset::truncate();
        Receiving::truncate();
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        // ==========================================
        // 2. SEED RECEIVINGS (Logistics Bay)
        // ==========================================
        
        // Accepted Receiving (Camera Traps)
        $rec1 = Receiving::create([
            'receive_no'        => 'RC-20250109-001',
            'supplier_name'     => 'UNI-TECHNOLOGIES SDN. BHD.',
            'supplier_address'  => 'LEVEL 2, INDUSTRY BUILDING TECHNOVATION PARK, UTM SKUDAI, 81310 JOHOR BAHRU',
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

        // Pending Receiving (NVIDIA GPUs)
        Receiving::create([
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
            'created_at'        => $now,
        ]);


        // ==========================================
        // 3. SEED ASSETS (KEW.PA-2 & KEW.PA-3)
        // ==========================================

        // Asset 1: Fixed Asset -> KEW.PA-2 (Lenovo Legion)
        $asset1 = Asset::create([
            'asset_tag'            => 'HT-0000197/2025',
            'name'                 => 'HIGH-END PERFORMANCE LAPTOP',
            'category'             => 'KOMPUTER',
            'asset_type'           => 'fixed_asset', // > RM1000
            'campus'               => 'utm_jb',
            'purchase_price'       => 11117.00,
            'location'             => 'FK - SKBSK-Aras 2-MAKMAL ACTUATOR AND AUTOMATION',
            'status'               => 'active',
            'supplier_name'        => 'UNI-TECHNOLOGIES SDN. BHD.',
            'supplier_address'     => 'LEVEL 2, INDUSTRY BUILDING TECHNOVATION PARK, UTM SKUDAI',
            'po_reference'         => 'PPTK090302112024000270',
            'do_reference'         => 'DO24/61',
            'warranty_expiry'      => $now->copy()->addYears(3),
            'received_date'        => $now->copy()->subDays(60),
            'custodian_name'       => 'Ts. Dr. Mohd Ibrahim',
            'model'                => 'LEGION PRO 5I',
            'brand'                => 'LENOVO',
            'serial_number'        => 'PF57G36K',
            'saga_id'              => 'B35201',
            'budget_vot'           => 'A.K090302.5500.07204',
            'requires_maintenance' => true,
        ]);

        // Asset 2: Inventory -> KEW.PA-3 (Camera Trap)
        $asset2 = Asset::create([
            'asset_tag'            => 'IN-0000251/2025',
            'name'                 => 'CAMERA TRAP',
            'category'             => 'ALATAN',
            'asset_type'           => 'inventory', // < RM1000
            'campus'               => 'utm_jb',
            'purchase_price'       => 948.75,
            'location'             => 'MAKMAL ECOLOGY',
            'status'               => 'active',
            'supplier_name'        => 'UNI-TECHNOLOGIES SDN. BHD.',
            'supplier_address'     => 'LEVEL 2, INDUSTRY BUILDING TECHNOVATION PARK',
            'po_reference'         => 'PPTK090302112024000270',
            'do_reference'         => 'DO24/61',
            'warranty_expiry'      => $now->copy()->addYears(1),
            'received_date'        => $now->copy()->subDays(30),
            'custodian_name'       => 'Prof. Madya Dr. Salina',
            'model'                => 'HC-900 LTE',
            'brand'                => 'TRAIL CAMERA',
            'serial_number'        => '2024/08/28/900/00110',
            'saga_id'              => 'B36901',
            'budget_vot'           => 'A.K090302.5500.07204',
            'requires_maintenance' => true,
        ]);


        // ==========================================
        // 4. SEED PLACEMENTS (PENEMPATAN)
        // ==========================================

        // Legion Laptop initial assignment
        AssetPlacement::create([
            'asset_id'       => $asset1->id,
            'custodian_name' => 'Ts. Dr. Mohd Ibrahim',
            'location'       => 'FK - SKBSK-Aras 2-MAKMAL ACTUATOR',
            'is_lokasi_luar' => false,
            'assigned_date'  => $asset1->received_date,
        ]);

        // Camera Trap initial assignment
        AssetPlacement::create([
            'asset_id'       => $asset2->id,
            'custodian_name' => 'Prof. Madya Dr. Salina',
            'location'       => 'MAKMAL ECOLOGY',
            'is_lokasi_luar' => false,
            'assigned_date'  => $asset2->received_date,
        ]);

        // Camera Trap is deployed to the forest (Lokasi Luar)
        AssetPlacement::create([
            'asset_id'       => $asset2->id,
            'custodian_name' => 'Prof. Madya Dr. Salina',
            'location'       => 'HUTAN SIMPAN PASOH, NEGERI SEMBILAN',
            'is_lokasi_luar' => true,
            'assigned_date'  => $now->copy()->subDays(5),
        ]);
        
        // Auto-update Asset 2's current location to match the latest placement
        $asset2->update([
            'location' => 'HUTAN SIMPAN PASOH, NEGERI SEMBILAN',
        ]);
    }
}