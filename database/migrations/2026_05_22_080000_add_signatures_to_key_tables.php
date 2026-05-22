<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add a JSONB 'signatures' column to key tables that
     * produce KEW.PA PDFs requiring captured signature images.
     *
     * Tables affected:
     *   - asset_disposals      (PA-18, PA-19)
     *   - asset_inspections    (PA-10, PA-11)
     *   - asset_maintenances   (PA-13, PA-14)
     *   - disposal_sales       (PA-21 → PA-27A)
     */
    public function up(): void
    {
        Schema::table('asset_disposals', function (Blueprint $table) {
            $table->json('signatures')->nullable()->after('notes');
        });

        Schema::table('asset_inspections', function (Blueprint $table) {
            $table->json('signatures')->nullable()->after('notes');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->json('signatures')->nullable()->after('notes');
        });

        Schema::table('disposal_sales', function (Blueprint $table) {
            $table->json('signatures')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_disposals', function (Blueprint $table) {
            $table->dropColumn('signatures');
        });

        Schema::table('asset_inspections', function (Blueprint $table) {
            $table->dropColumn('signatures');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->dropColumn('signatures');
        });

        Schema::table('disposal_sales', function (Blueprint $table) {
            $table->dropColumn('signatures');
        });
    }
};
