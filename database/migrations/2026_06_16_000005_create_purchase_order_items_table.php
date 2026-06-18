<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create the purchase_order_items table - individual line items
     * from a Purchase Order, with unit prices and category codes.
     */
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_code')->nullable();           // e.g. "1", "1.1.1.1" from PO document
            $table->text('description');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('category')->nullable();            // e.g. "OFFICE EQUIPMENTS", "SMART MANUFACTURING LAB"
            $table->integer('quantity_ordered')->default(1);
            $table->string('unit')->nullable();                // "unit", "set", "lot"
            $table->decimal('unit_price', 14, 2)->nullable();
            $table->decimal('total_price', 14, 2)->nullable();
            $table->string('status')->default('pending');      // pending | received | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            // Link to DO line items when verified
            $table->foreignId('do_line_item_id')->nullable()->constrained()->nullOnDelete();
        });

        // Add purchase_order_id FK to delivery_orders
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->after('supplier_id')
                  ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_order_id');
        });
        Schema::dropIfExists('purchase_order_items');
    }
};
