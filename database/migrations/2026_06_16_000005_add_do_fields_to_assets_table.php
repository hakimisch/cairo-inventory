<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add DO tracking fields to the assets table:
     *   - do_line_item_id: links asset back to its originating DO line item
     *   - verified_by_scan: tracks if asset was created through scan verification
     *   - scan_verified_at: timestamp of scan verification
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('do_line_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();

            $table->boolean('verified_by_scan')
                  ->default(false)
                  ->after('do_line_item_id');

            $table->timestamp('scan_verified_at')
                  ->nullable()
                  ->after('verified_by_scan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['do_line_item_id']);
            $table->dropColumn(['do_line_item_id', 'verified_by_scan', 'scan_verified_at']);
        });
    }
};
