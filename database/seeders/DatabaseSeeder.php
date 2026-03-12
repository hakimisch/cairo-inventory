<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Receiving;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed the Receiving List (KEW.PA-1 / KEW.PA-2)
        // This shows items currently being inspected at the lab entrance
        Receiving::create([
            'receive_no' => 'RC-2026-001',
            'supplier_name' => 'NVIDIA Malaysia S/B',
            'supplier_address' => 'Level 15, Menara Binjai, No. 2, Jalan Binjai, 50450 Kuala Lumpur',
            'delivery_order_no' => 'DO-NV-99812',
            'purchase_order_no' => 'PO-CAIRO-2026-001',
            'item_description' => 'NVIDIA DGX Station A100',
            'quantity_ordered' => 1,
            'quantity_received' => 1,
            'status' => 'pending', // Demo: Use this to show the "Terima" workflow
        ]);

        Receiving::create([
            'receive_no' => 'RC-2026-002',
            'supplier_name' => 'Dell Technologies Malaysia',
            'supplier_address' => 'Plot 76, Mukim 11, Bukit Tengah Industrial Park, 14000 Bukit Mertajam, Pulau Pinang',
            'delivery_order_no' => 'DO-DELL-5521',
            'purchase_order_no' => 'PO-CAIRO-2026-002',
            'item_description' => 'Precision 7920 Tower Workstation',
            'quantity_ordered' => 5,
            'quantity_received' => 5,
            'status' => 'pending',
        ]);

        // 2. Seed the Main Inventory (KEW.PA-3)
        // These are items already "Accepted" and registered in the system
        Asset::create([
            'asset_tag' => 'CAIRO/2026/H/01',
            'name' => 'High-Performance Computing Cluster (HPC)',
            'category' => 'Server',
            'national_code' => '5610-17-030-2211', // Based on Lampiran E 
            'purchase_price' => 1250000.00,
            'location' => 'Server Room 1, CAIRO',
            'status' => 'active',
            'supplier_name' => 'Syarikat Daya Maju S/B',
            'supplier_address' => 'No.1 Jalan SS3, 13000 S.Alam Selangor', // Based on KEW.PA-1 source [cite: 28]
            'po_reference' => 'LO000007317',
            'warranty_period' => '36 Months',
            'received_date' => '2026-03-08',
        ]);

        Asset::create([
            'asset_tag' => 'CAIRO/2026/H/02',
            'name' => 'Unit GPU Node Unit 6',
            'category' => 'GPU Node',
            'national_code' => '5610-17-030-2212',
            'purchase_price' => 37762.00,
            'location' => 'AI Lab',
            'status' => 'active',
            'supplier_name' => 'NVIDIA Malaysia S/B',
            'supplier_address' => 'Level 15, Menara Binjai, Kuala Lumpur',
            'po_reference' => 'PO-CAIRO-2025-099',
            'warranty_period' => '24 Months',
            'received_date' => '2026-01-15',
        ]);
    }
}