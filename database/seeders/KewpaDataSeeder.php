<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\AssetInspection;
use App\Models\AssetLossReport;
use App\Models\AssetMaintenance;
use App\Models\AssetPlacement;
use App\Models\AssetTransfer;
use App\Models\AssetUpgrade;
use App\Models\CommitteeAppointment;
use App\Models\DamageReport;
use App\Models\DisposalSale;
use App\Models\DisposalSaleItem;
use App\Models\Receiving;
use App\Models\SaleBid;
use App\Models\User;
use App\Models\VehicleDisposalAssessment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KewpaDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Get existing users & assets ───────────────────────────────────────
        $staff  = User::where('email', 'ibrahim@cairo.utm')->first();
        $admin  = User::where('email', 'admin@cairo.utm')->first();
        $assets = Asset::all();

        if ($assets->isEmpty() || !$staff) {
            $this->command->warn('Please run DatabaseSeeder first to create users and assets.');
            return;
        }

        $staffName  = $staff->name;
        $adminName  = $admin?->name ?? $staffName;

        // ─── Status pools ──────────────────────────────────────────────────────
        $maintenanceStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $disposalStatuses    = ['draft', 'committee_review', 'approved', 'completed', 'cancelled'];
        $inspectionStatuses  = ['pending', 'completed', 'failed'];
        $transferStatuses    = ['pending', 'approved', 'completed', 'cancelled'];
        $damageStatuses      = ['pending', 'investigating', 'resolved'];
        $lossStatuses        = ['under_investigation', 'committee_review', 'approved', 'closed'];
        $placementStatuses   = ['active', 'returned', 'overdue'];
        $vehicleStatuses     = ['draft', 'submitted', 'approved', 'rejected'];
        $receivingStatuses   = ['pending', 'accepted', 'rejected'];
        $disposalSaleStatuses = ['draft', 'open', 'closed', 'cancelled'];

        // ─── 1. Receivings (KEW.PA-1 / PA-1A) — 10 records ────────────────────
        // Start from 003 to avoid conflict with DatabaseSeeder's REC-2026-001 and 002
        for ($i = 3; $i <= 12; $i++) {
            Receiving::create([
                'receive_no'            => "REC-2026-" . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'supplier_name'         => fake()->company(),
                'supplier_address'      => fake()->address(),
                'purchase_order_no'     => "PO-2026-" . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'delivery_order_no'     => "DO-2026-" . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'invoice_no'            => "INV-2026-" . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'item_description'      => fake()->words(3, true),
                'quantity_ordered'      => rand(1, 10),
                'quantity_received'     => rand(1, 10),
                'unit_price'            => fake()->randomFloat(2, 50, 5000),
                'total_price'           => 0, // will compute below
                'status'                => $receivingStatuses[array_rand($receivingStatuses)],
                'receiver_name'         => $staffName,
                'receiver_position'     => 'Pegawai Teknologi Maklumat',
                'technical_officer_name'  => $adminName,
                'technical_officer_position' => 'Ketua Unit ICT',
            ]);
        }
        // Fix total_price for receivings
        Receiving::all()->each(function ($r) {
            $r->update(['total_price' => $r->quantity_received * $r->unit_price]);
        });

        // ─── 2. Asset Placements (KEW.PA-7 / PA-9A) — 10 records ──────────────
        foreach ($assets->take(10) as $asset) {
            AssetPlacement::create([
                'asset_id'             => $asset->id,
                'custodian_name'       => $staffName,
                'staff_id'             => 'STF-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT),
                'borrower_phone'       => '01' . rand(10000000, 99999999),
                'matric_no'            => 'A' . rand(100000, 999999),
                'authorizer_name'      => $adminName,
                'location'             => fake()->randomElement(['Makmal AI', 'Stor Utama', 'Bilik Pelayan', 'Pejabat', 'Makmal Rangkaian']),
                'quantity_placed'      => rand(1, 3),
                'is_lokasi_luar'       => (bool) rand(0, 1),
                'assigned_date'        => Carbon::now()->subDays(rand(1, 90)),
                'expected_return_date' => Carbon::now()->addDays(rand(1, 30)),
                'returned_date'        => null,
            ]);
        }

        // ─── 3. Damage Reports (KEW.PA-9) — 10 records ────────────────────────
        foreach ($assets->take(10) as $asset) {
            DamageReport::create([
                'asset_id'                  => $asset->id,
                'reported_by'               => $staffName,
                'damage_date'               => Carbon::now()->subDays(rand(1, 60)),
                'last_user'                 => $staffName,
                'previous_maintenance_cost' => fake()->randomFloat(2, 0, 2000),
                'damage_description'        => fake()->sentence(8),
                'status'                    => $damageStatuses[array_rand($damageStatuses)],
                'technical_notes'           => fake()->optional(0.7)->sentence(6),
                'recommendation'            => fake()->optional(0.5)->sentence(5),
                'hod_decision'              => fake()->optional(0.4)->randomElement(['approve_repair', 'replace', 'dispose', 'dismiss']),
            ]);
        }

        // ─── 4. Asset Inspections (KEW.PA-10 / PA-11) — 10 records ────────────
        foreach ($assets->take(10) as $asset) {
            AssetInspection::create([
                'asset_id'           => $asset->id,
                'inspection_date'    => Carbon::now()->subDays(rand(1, 90)),
                'status'             => $inspectionStatuses[array_rand($inspectionStatuses)],
                'inspector_name'     => $adminName,
                'notes'              => fake()->optional(0.8)->sentence(6),
                'is_record_complete' => (bool) rand(0, 1),
                'is_record_updated'  => (bool) rand(0, 1),
                'actual_location'    => fake()->optional(0.7)->randomElement(['Makmal AI', 'Stor Utama', 'Bilik Pelayan']),
                'actual_quantity'    => rand(1, 5),
            ]);
        }

        // ─── 5. Asset Maintenances (KEW.PA-13 / PA-14) — 10 records ───────────
        foreach ($assets->take(10) as $asset) {
            AssetMaintenance::create([
                'asset_id'         => $asset->id,
                'maintenance_date' => Carbon::now()->subDays(rand(1, 180)),
                'description'      => fake()->sentence(6),
                'contract_no'      => 'CONT-' . strtoupper(fake()->bothify('??###')),
                'company_name'     => fake()->company(),
                'cost'             => fake()->randomFloat(2, 100, 10000),
                'status'           => $maintenanceStatuses[array_rand($maintenanceStatuses)],
                'notes'            => fake()->optional(0.6)->sentence(4),
            ]);
        }

        // ─── 6. Asset Transfers (KEW.PA-6) — 10 records ───────────────────────
        $locations = ['Makmal AI', 'Stor Utama', 'Bilik Pelayan', 'Pejabat', 'Makmal Rangkaian', 'Bilik Pensyarah', 'Dewan Kuliah'];
        foreach ($assets->take(10) as $asset) {
            $from = $locations[array_rand($locations)];
            $to   = $locations[array_rand($locations)];
            while ($to === $from) {
                $to = $locations[array_rand($locations)];
            }
            AssetTransfer::create([
                'asset_id'       => $asset->id,
                'from_location'  => $from,
                'to_location'    => $to,
                'from_custodian' => $staffName,
                'to_custodian'   => $adminName,
                'transfer_date'  => Carbon::now()->subDays(rand(1, 60)),
                'reference_no'   => 'TRF-' . strtoupper(fake()->bothify('??###')),
                'status'         => $transferStatuses[array_rand($transferStatuses)],
                'reason'         => fake()->sentence(4),
                'notes'          => fake()->optional(0.5)->sentence(3),
            ]);
        }

        // ─── 7. Asset Disposals (KEW.PA-17 / PA-18 / PA-19) — 10 records ──────
        $disposalMethods = ['Tanam', 'Bakar', 'Tenggelam', 'Jualan', 'Pindahan'];
        foreach ($assets->take(10) as $asset) {
            AssetDisposal::create([
                'asset_id'          => $asset->id,
                'request_reason'    => fake()->sentence(6),
                'committee_decision'=> fake()->optional(0.6)->sentence(4),
                'disposal_method'   => $disposalMethods[array_rand($disposalMethods)],
                'disposal_date'     => Carbon::now()->subDays(rand(1, 90)),
                'approval_reference'=> 'APP-' . strtoupper(fake()->bothify('??###')),
                'status'            => $disposalStatuses[array_rand($disposalStatuses)],
                'notes'             => fake()->optional(0.5)->sentence(3),
            ]);
        }

        // ─── 8. Asset Loss Reports (KEW.PA-28 → PA-32) — 10 records ───────────
        $lossMethods = ['hilang', 'rosak', 'musnah', 'dicuri'];
        foreach ($assets->take(10) as $asset) {
            AssetLossReport::create([
                'asset_id'             => $asset->id,
                'incident_location'    => fake()->randomElement(['Makmal AI', 'Stor Utama', 'Bilik Pelayan', 'Pejabat', 'Luar Kampus']),
                'loss_date'            => Carbon::now()->subDays(rand(1, 120)),
                'loss_method'          => $lossMethods[array_rand($lossMethods)],
                'last_officer'         => $staffName,
                'police_report_no'     => fake()->optional(0.7)->bothify('PR-####/####'),
                'current_value'        => fake()->randomFloat(2, 100, 20000),
                'action_type'          => fake()->randomElement(['surcharge', 'write_off']),
                'write_off_value'      => fake()->optional(0.5)->randomFloat(2, 50, 15000),
                'surcharge_amount'     => fake()->optional(0.5)->randomFloat(2, 50, 5000),
                'applied_date'         => Carbon::now()->subDays(rand(1, 30)),
                'investigation_summary' => fake()->optional(0.7)->sentence(8),
                'approval_reference'   => 'AL-APP-' . strtoupper(fake()->bothify('??###')),
                'status'               => $lossStatuses[array_rand($lossStatuses)],
                'notes'                => fake()->optional(0.5)->sentence(3),
            ]);
        }

        // ─── 9. Vehicle Disposal Assessments (KEW.PA-16) — 10 records ─────────
        $vehicleBrands  = ['Proton', 'Perodua', 'Toyota', 'Honda', 'Nissan', 'Mitsubishi'];
        $vehicleModels  = ['Saga', 'Myvi', 'Camry', 'Civic', 'Navara', 'Pajero'];
        $fuelTypes      = ['Petrol', 'Diesel', 'Hybrid', 'Electric'];
        $colors         = ['Putih', 'Hitam', 'Perak', 'Biru', 'Merah', 'Kelabu'];
        foreach ($assets->take(10) as $asset) {
            VehicleDisposalAssessment::create([
                'asset_id'         => $asset->id,
                'plate_no'         => strtoupper(fake()->bothify('?? ####')),
                'chassis_no'       => strtoupper(fake()->bothify('CHS-########')),
                'engine_no'        => strtoupper(fake()->bothify('ENG-########')),
                'vehicle_brand'    => $vehicleBrands[array_rand($vehicleBrands)],
                'vehicle_model'    => $vehicleModels[array_rand($vehicleModels)],
                'vehicle_year'     => rand(2010, 2024),
                'road_tax_expiry'  => Carbon::now()->addMonths(rand(-6, 12)),
                'engine_capacity'  => fake()->randomElement(['1300cc', '1500cc', '1800cc', '2000cc', '2500cc']),
                'fuel_type'        => $fuelTypes[array_rand($fuelTypes)],
                'vehicle_color'    => $colors[array_rand($colors)],
                'condition_report' => fake()->sentence(8),
                'estimated_value'  => fake()->randomFloat(2, 1000, 100000),
                'assessment_date'  => Carbon::now()->subDays(rand(1, 60)),
                'assessor_name'    => $adminName,
                'assessor_position'=> 'Penilai Aset',
                'recommendation'   => fake()->optional(0.7)->randomElement(['jual', 'lupus', 'baiki', 'simpan']),
                'status'           => $vehicleStatuses[array_rand($vehicleStatuses)],
                'notes'            => fake()->optional(0.5)->sentence(3),
            ]);
        }

        // ─── 10. Disposal Sales (KEW.PA-21 → PA-27A) — 10 records ─────────────
        $disposals = AssetDisposal::all();
        foreach ($disposals->take(10) as $disposal) {
            DisposalSale::create([
                'asset_disposal_id'   => $disposal->id,
                'sale_type'           => fake()->randomElement(['Tawaran', 'Sebutharga', 'Lelongan']),
                'sale_reference'      => 'SALE-' . strtoupper(fake()->bothify('??###')),
                'sale_date'           => Carbon::now()->subDays(rand(1, 60)),
                'sale_location'       => fake()->address(),
                'sale_officer'        => $staffName,
                'description'         => fake()->sentence(6),
                'terms_conditions'    => fake()->optional(0.6)->sentence(4),
                'deposit_required'    => fake()->randomFloat(2, 100, 5000),
                'sale_status'         => fake()->randomElement(['open', 'closed', 'cancelled']),
                'decision_date'       => Carbon::now()->subDays(rand(1, 30)),
                'decision_notes'      => fake()->optional(0.5)->sentence(4),
                'report_date'         => Carbon::now()->subDays(rand(1, 20)),
                'report_notes'        => fake()->optional(0.5)->sentence(4),
                'certificate_date'    => Carbon::now()->subDays(rand(1, 10)),
                'certificate_type'    => fake()->randomElement(['kewpa24', 'kewpa25', 'kewpa26']),
                'certificate_reference' => 'CERT-' . strtoupper(fake()->bothify('??###')),
                'status'              => $disposalSaleStatuses[array_rand($disposalSaleStatuses)],
                'notes'               => fake()->optional(0.5)->sentence(3),
            ]);
        }

        $this->command->info('✓ KewpaDataSeeder: 10+ records seeded for each KEWPA module.');

        // ═══════════════════════════════════════════════════════════════════════
        // PHASE 2: Previously-empty tables
        // ═══════════════════════════════════════════════════════════════════════

        // ─── 11. Asset Upgrades — 1 per existing asset ────────────────────────
        $upgradeDesc     = ['Tambah RAM 16GB', 'Naik taraf SSD 512GB', 'Ganti bateri', 'Tambah storan HDD 1TB', 'Naik taraf OS', 'Ganti skrin LCD', 'Tambah GPU', 'Naik taraf CPU', 'Ganti papan kekunci', 'Tambah modul WiFi'];
        $warrantyOptions = ['1 tahun', '2 tahun', '3 tahun', '6 bulan', '12 bulan'];
        foreach ($assets as $i => $asset) {
            AssetUpgrade::create([
                'asset_id'       => $asset->id,
                'date'           => Carbon::now()->subDays(rand(30, 365)),
                'description'    => $upgradeDesc[$i % count($upgradeDesc)],
                'warranty_period'=> $warrantyOptions[array_rand($warrantyOptions)],
                'cost'           => fake()->randomFloat(2, 100, 3000),
            ]);
        }

        // ─── 12. Disposal Sale Items — 2–3 items per sale ─────────────────────
        $sales           = DisposalSale::whereIn('status', ['open', 'closed', 'draft'])->get();
        $itemStatuses    = ['available', 'sold', 'unsold', 'withdrawn'];
        foreach ($sales as $sale) {
            $itemCount = rand(2, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $assetForItem = $assets->random();
                DisposalSaleItem::create([
                    'disposal_sale_id' => $sale->id,
                    'asset_id'         => $assetForItem->id,
                    'item_description' => fake()->sentence(4),
                    'quantity'         => rand(1, 5),
                    'reserve_price'    => fake()->randomFloat(2, 50, 5000),
                    'estimated_value'  => fake()->randomFloat(2, 100, 8000),
                    'lot_number'       => 'LOT-' . strtoupper(fake()->bothify('??###')),
                    'status'           => $itemStatuses[array_rand($itemStatuses)],
                    'notes'            => fake()->optional(0.4)->sentence(3),
                ]);
            }
        }

        // ─── 13. Sale Bids — 1–2 bids per sale item ───────────────────────────
        $saleItems       = DisposalSaleItem::all();
        $bidStatuses     = ['pending', 'accepted', 'rejected', 'paid', 'completed'];
        $bidNames        = ['Ahmad Faizal', 'Siti Nurhaliza', 'Mohd Kamal', 'Norazlin Rahim', 'Tan Wei Ming', 'Rajesh Kumar', 'Lim Siew Ling', 'Abdul Rahman'];
        foreach ($saleItems as $item) {
            $bidPerItem = rand(1, 2);
            for ($k = 0; $k < $bidPerItem; $k++) {
                $isWinner = ($k === 0 && $bidPerItem === 1); // winner if only 1 bid
                SaleBid::create([
                    'disposal_sale_item_id' => $item->id,
                    'bidder_name'           => $bidNames[array_rand($bidNames)],
                    'bidder_ic'             => fake()->numerify('########-##-####'),
                    'bidder_phone'          => '01' . rand(10000000, 99999999),
                    'bidder_address'        => fake()->address(),
                    'bid_amount'            => $item->reserve_price + fake()->randomFloat(2, 10, 2000),
                    'bid_date'              => Carbon::now()->subDays(rand(1, 30)),
                    'deposit_paid'          => (bool) rand(0, 1),
                    'deposit_amount'        => fake()->randomFloat(2, 50, 500),
                    'is_winner'             => $isWinner,
                    'award_date'            => $isWinner ? Carbon::now()->subDays(rand(1, 14)) : null,
                    'payment_date'          => $isWinner && rand(0, 1) ? Carbon::now()->subDays(rand(1, 7)) : null,
                    'payment_received'      => $isWinner && rand(0, 1),
                    'status'                => $bidStatuses[array_rand($bidStatuses)],
                    'notes'                 => fake()->optional(0.3)->sentence(3),
                ]);
            }
        }

        // ─── 14. Committee Appointments — 2–3 per disposal (PA-15) ─────────────
        $disposals       = AssetDisposal::whereIn('status', ['committee_review', 'approved', 'completed'])->get();
        $committeeRoles  = ['chairman', 'member', 'secretary', 'member', 'member'];
        foreach ($disposals as $disposal) {
            $memberCount = rand(2, 3);
            for ($m = 0; $m < $memberCount; $m++) {
                $appointUser = ($m === 0) ? $admin : $staff; // chairman = admin
                // Vary users to avoid duplicate user+disposal constraint if any
                if ($m > 0) {
                    $appointUser = User::inRandomOrder()->first() ?? $staff;
                }
                CommitteeAppointment::create([
                    'user_id'          => $appointUser->id,
                    'appointable_type' => AssetDisposal::class,
                    'appointable_id'   => $disposal->id,
                    'role'             => $committeeRoles[$m % count($committeeRoles)],
                    'valid_from'       => Carbon::now()->subDays(rand(30, 90)),
                    'valid_until'      => Carbon::now()->addDays(rand(30, 180)),
                    'notes'            => fake()->optional(0.5)->sentence(4),
                ]);
            }
        }

        $this->command->info('✓ Phase 2 seeded: ' .
            AssetUpgrade::count() . ' upgrades, ' .
            DisposalSaleItem::count() . ' sale items, ' .
            SaleBid::count() . ' bids, ' .
            CommitteeAppointment::count() . ' committee appointments.');
    }
}
