<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enrich master_items with verification and asset-type fields.
     */
    public function up(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            // ── Verification tracking ─────────────────────────────────────────
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->after('verified_at')
                  ->constrained('users')
                  ->nullOnDelete();

            // ── Asset classification ──────────────────────────────────────────
            // 'fixed_asset' (PA-3, ≥ RM 2k) or 'inventory' (PA-2, < RM 2k)
            $table->string('asset_type')->nullable()->after('verified_by');

            // ── Invoice reference for PA-1 ────────────────────────────────────
            $table->string('invoice_no')->nullable()->after('do_no');
        });
    }

    public function down(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'verified_by', 'asset_type', 'invoice_no']);
        });
    }
};
