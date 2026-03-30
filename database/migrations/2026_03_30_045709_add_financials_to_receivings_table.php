<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('purchase_order_no');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity_received');
            $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            //
        });
    }
};
