<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft-delete (deleted_at) columns to all deletable tables.
     */
    public function up(): void
    {
        $tables = [
            'assets',
            'receivings',
            'suppliers',
            'master_items',
            'scans',
            'asset_disposals',
            'asset_inspections',
            'asset_loss_reports',
            'asset_maintenances',
            'asset_placements',
            'asset_transfers',
            'asset_upgrades',
            'damage_reports',
            'vehicle_disposal_assessments',
            'disposal_sales',
            'disposal_sale_items',
            'sale_bids',
            'committee_appointments',
            'final_loss_reports',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse: drop deleted_at from all tables.
     */
    public function down(): void
    {
        $tables = [
            'assets',
            'receivings',
            'suppliers',
            'master_items',
            'scans',
            'asset_disposals',
            'asset_inspections',
            'asset_loss_reports',
            'asset_maintenances',
            'asset_placements',
            'asset_transfers',
            'asset_upgrades',
            'damage_reports',
            'vehicle_disposal_assessments',
            'disposal_sales',
            'disposal_sale_items',
            'sale_bids',
            'committee_appointments',
            'final_loss_reports',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropSoftDeletes();
                });
            }
        }
    }
};
