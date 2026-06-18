<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add unit_price and po_item_code to assets table.
     *
     * - unit_price: per-unit cost from the PO (needed for PA-2 valuation)
     * - po_item_code: the item number from the PO (e.g. "31" for GPU Server)
     *   enables PO→asset traceability
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('unit_price', 14, 2)->nullable()->after('purchase_price');
            $table->string('po_item_code')->nullable()->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'po_item_code']);
        });
    }
};
