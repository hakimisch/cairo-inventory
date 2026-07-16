<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link receivings and assets back to master_items.
     * Also add unit_price + total_price to receivings for PA-1 completeness.
     */
    public function up(): void
    {
        // ── Link receivings to master_items ───────────────────────────────────
        Schema::table('receivings', function (Blueprint $table) {
            $table->foreignId('master_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();

            // Add pricing columns needed for auto-PA-1 creation
            if (!Schema::hasColumn('receivings', 'unit_price')) {
                $table->decimal('unit_price', 14, 2)->nullable()->after('quantity_received');
            }
            if (!Schema::hasColumn('receivings', 'total_price')) {
                $table->decimal('total_price', 14, 2)->nullable()->after('unit_price');
            }
        });

        // ── Link assets to master_items, drop the old do_line_item_id ─────────
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('master_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();

            if (Schema::hasColumn('assets', 'verified_by_scan')) {
                $table->boolean('verified_by_scan')->default(false)->change();
            }
            if (Schema::hasColumn('assets', 'scan_verified_at')) {
                $table->timestamp('scan_verified_at')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_item_id');
        });

        Schema::table('receivings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_item_id');
            $table->dropColumn(['unit_price', 'total_price']);
        });
    }
};
