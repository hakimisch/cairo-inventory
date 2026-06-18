<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create the do_line_items table - individual items from a Delivery Order
     * with serial numbers, quantities, and scan verification status.
     */
    public function up(): void
    {
        Schema::create('do_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->string('category')->nullable();          // e.g. "Multimedia Equipments For Office"
            $table->string('item_code')->nullable();         // e.g. "1.1.4.8" from DO document
            $table->text('description');
            $table->string('brand')->nullable();             // e.g. "HIKVISION", "DELL"
            $table->string('model')->nullable();             // e.g. "DS-3E1318P", "QCM1250"
            $table->string('serial_number')->nullable();     // single S/N or comma-separated
            $table->integer('quantity_ordered')->default(1);
            $table->integer('quantity_received')->default(0);
            $table->string('unit')->nullable();              // "unit", "box", "set", "spool"
            $table->string('status')->default('pending');    // pending | received | shortage | damaged | verified
            $table->foreignId('scan_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('do_line_items');
    }
};
