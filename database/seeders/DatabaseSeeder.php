<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Receiving;
use App\Models\AssetPlacement;
use App\Models\DamageReport;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Users ────────────────────────────────────────────────────────
        
        // YOUR CUSTOM ADMIN ACCOUNT
        $superAdmin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $admin = User::create([
            'name'     => 'Pentadbir Sistem CAIRO',
            'email'    => 'admin@cairo.utm',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $staff = User::create([
            'name'     => 'Ts. Dr. Mohd Ibrahim',
            'email'    => 'ibrahim@cairo.utm',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // ─── 2. Receivings (KEW.PA-1) ────────────────────────────────────────
        Receiving::create([
            'receive_no'        => 'REC-2026-001',
            'supplier_name'     => 'TechZone Solutions',
            'supplier_address'  => '123 Tech Park, Cyberjaya', // FIX: Added missing field
            'purchase_order_no' => 'PO-2026-001',            // FIX: Added missing field
            'delivery_order_no' => 'DO-2026-001',            // FIX: Added missing field
            'invoice_no'        => 'INV-2026-001',           // FIX: Added missing field
            'item_description'  => 'NVIDIA RTX 4070 GPU Node',
            'quantity_ordered'  => 2,
            'quantity_received' => 2,
            'unit_price'        => 3200.00,
            'total_price'       => 6400.00,
            'status'            => 'pending',
        ]);

        Receiving::create([
            'receive_no'        => 'REC-2026-002',
            'supplier_name'     => 'Global Tech Supplies',
            'supplier_address'  => '45 Indahpura, Johor Bahru',// FIX: Added missing field
            'purchase_order_no' => 'PO-2026-002',            // FIX: Added missing field
            'delivery_order_no' => 'DO-2026-002',            // FIX: Added missing field
            'invoice_no'        => 'INV-2026-002',           // FIX: Added missing field
            'item_description'  => 'Kabel Rangkaian Cat6 (Gulung)',
            'quantity_ordered'  => 5,
            'quantity_received' => 5,
            'unit_price'        => 150.00,
            'total_price'       => 750.00,
            'status'            => 'pending',
        ]);

        // ─── 3. Assets (KEW.PA-2 & KEW.PA-3) ─────────────────────────────────
        $asset1 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/01',
            'national_code'  => 'H-AI-001',
            'name'           => 'High-Performance AI Workstation',
            'category'       => 'Workstation',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_kl',
            'purchase_price' => 12500.00,
            'location'       => 'Makmal AI',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(2),
            'warranty_expiry'=> Carbon::now()->addYears(2),
            'custodian_name' => $staff->name,
            'brand'          => 'Lenovo',
            'model'          => 'Legion Pro 7',
            'serial_number'  => 'LR-998234',
            'requires_maintenance' => true,
        ]);

        $asset2 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/I/01',
            'national_code'  => 'I-CAM-001',
            'name'           => 'Outdoor Camera Trap',
            'category'       => 'Sensor',
            'asset_type'     => 'inventory',
            'campus'         => 'utm_jb',
            'purchase_price' => 948.00,
            'location'       => 'Stor Utama',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(1),
            'custodian_name' => $admin->name,
            'brand'          => 'Bushnell',
            'model'          => 'Core DS',
            'serial_number'  => 'BSH-1122',
            'requires_maintenance' => false,
        ]);

        // Damaged Asset
        $asset3 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/02',
            'national_code'  => 'H-SVR-001',
            'name'           => 'Database Server (Laravel/PostgreSQL)',
            'category'       => 'Server',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_kl',
            'purchase_price' => 18000.00,
            'location'       => 'Bilik Pelayan',
            'status'         => 'repair', // Triggers the dashboard alert
            'received_date'  => Carbon::now()->subYears(1),
            'warranty_expiry'=> Carbon::now()->subDays(10), // Expired
            'custodian_name' => $staff->name,
            'brand'          => 'Dell',
            'model'          => 'PowerEdge R740',
            'serial_number'  => 'DL-SVR-8821',
            'requires_maintenance' => true,
        ]);

        // ─── Additional Assets (for KewpaDataSeeder to have 10+ per module) ──
        $asset4 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/03',
            'national_code'  => 'H-PC-001',
            'name'           => 'Desktop PC - Makmal Rangkaian',
            'category'       => 'Desktop',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_kl',
            'purchase_price' => 3500.00,
            'location'       => 'Makmal Rangkaian',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(3),
            'warranty_expiry'=> Carbon::now()->addYears(1),
            'custodian_name' => $staff->name,
            'brand'          => 'HP',
            'model'          => 'ProDesk 400',
            'serial_number'  => 'HP-PD-7721',
            'requires_maintenance' => true,
        ]);

        $asset5 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/I/02',
            'national_code'  => 'I-SW-001',
            'name'           => 'Microsoft Office 365 License (1 Year)',
            'category'       => 'Software',
            'asset_type'     => 'inventory',
            'campus'         => 'utm_kl',
            'purchase_price' => 450.00,
            'location'       => 'Stor Digital',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subDays(15),
            'custodian_name' => $admin->name,
            'brand'          => 'Microsoft',
            'model'          => 'Office 365 E3',
            'serial_number'  => 'O365-E3-001',
            'requires_maintenance' => false,
        ]);

        $asset6 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/04',
            'national_code'  => 'H-SW-001',
            'name'           => 'Cisco Catalyst 9300 Switch',
            'category'       => 'Rangkaian',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_jb',
            'purchase_price' => 8500.00,
            'location'       => 'Bilik Rangkaian',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(6),
            'warranty_expiry'=> Carbon::now()->addYears(3),
            'custodian_name' => $staff->name,
            'brand'          => 'Cisco',
            'model'          => 'Catalyst 9300',
            'serial_number'  => 'CSCO-9300-554',
            'requires_maintenance' => true,
        ]);

        $asset7 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/I/03',
            'national_code'  => 'I-FURN-001',
            'name'           => 'Ergonomic Office Chair',
            'category'       => 'Perabot',
            'asset_type'     => 'inventory',
            'campus'         => 'utm_kl',
            'purchase_price' => 1200.00,
            'location'       => 'Pejabat',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(2),
            'custodian_name' => $admin->name,
            'brand'          => 'Herman Miller',
            'model'          => 'Aeron',
            'serial_number'  => 'HM-AERON-221',
            'requires_maintenance' => false,
        ]);

        $asset8 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/05',
            'national_code'  => 'H-PRINT-001',
            'name'           => 'Multifunction Printer (A3)',
            'category'       => 'Pencetak',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_jb',
            'purchase_price' => 4200.00,
            'location'       => 'Pejabat',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(4),
            'warranty_expiry'=> Carbon::now()->addYears(2),
            'custodian_name' => $staff->name,
            'brand'          => 'Canon',
            'model'          => 'imageRUNNER 2625i',
            'serial_number'  => 'CN-IR2625-887',
            'requires_maintenance' => true,
        ]);

        $asset9 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/I/04',
            'national_code'  => 'I-TOOL-001',
            'name'           => 'Network Tool Kit (Set)',
            'category'       => 'Alatan',
            'asset_type'     => 'inventory',
            'campus'         => 'utm_kl',
            'purchase_price' => 680.00,
            'location'       => 'Stor Utama',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(1),
            'custodian_name' => $admin->name,
            'brand'          => 'Klein Tools',
            'model'          => 'VDV500-820',
            'serial_number'  => 'KT-VDV-332',
            'requires_maintenance' => false,
        ]);

        $asset10 = Asset::create([
            'asset_tag'      => 'CAIRO/2026/H/06',
            'national_code'  => 'H-UPS-001',
            'name'           => 'UPS 3000VA (Bilik Pelayan)',
            'category'       => 'Elektrikal',
            'asset_type'     => 'fixed_asset',
            'campus'         => 'utm_kl',
            'purchase_price' => 6200.00,
            'location'       => 'Bilik Pelayan',
            'status'         => 'active',
            'received_date'  => Carbon::now()->subMonths(8),
            'warranty_expiry'=> Carbon::now()->addYears(1),
            'custodian_name' => $staff->name,
            'brand'          => 'APC',
            'model'          => 'Smart-UPS 3000VA',
            'serial_number'  => 'APC-SU3000-112',
            'requires_maintenance' => true,
        ]);

        // ─── 4. Placements (KEW.PA-7 Data) ───────────────────────────────────
        AssetPlacement::create([
            'asset_id'       => $asset1->id,
            'custodian_name' => $staff->name,
            'location'       => 'Makmal AI',
            'is_lokasi_luar' => false,
            'assigned_date'  => Carbon::now()->subMonths(2),
        ]);

        // ─── 5. Damage Reports (KEW.PA-9 Data) ───────────────────────────────
        DamageReport::create([
            'asset_id'                  => $asset3->id,
            'reported_by'               => $staff->name,
            'damage_date'               => Carbon::now()->subDays(2),
            'last_user'                 => $staff->name,
            'previous_maintenance_cost' => 0.00,
            'damage_description'        => 'Kipas penyejuk (cooling fan) rosak menyebabkan server overheat dan terpadam secara automatik. Suhu hotspot GPU melebihi had selamat.',
            'status'                    => 'pending',
        ]);

        // ─── 6. Seed 10+ records for every KEWPA module ──────────────────────
        $this->call(KewpaDataSeeder::class);

        // ─── 7. Seed Phase 2 suppliers ──────────────────────────────────────
        $this->call(SupplierSeeder::class);
    }
}