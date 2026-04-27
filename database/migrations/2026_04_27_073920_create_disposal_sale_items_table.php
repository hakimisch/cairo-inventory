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
        Schema::create('disposal_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->text('item_description')->nullable();         // Description of item/lot
            $table->integer('quantity')->default(1);              // Quantity in this lot
            $table->decimal('reserve_price', 12, 2)->nullable();  // Minimum/reserve price
            $table->decimal('estimated_value', 12, 2)->nullable(); // Estimated value
            $table->string('lot_number')->nullable();             // Lot number for auction
            $table->string('status')->default('available');       // Available / Sold / Unsold / Withdrawn
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_sale_items');
    }
};
