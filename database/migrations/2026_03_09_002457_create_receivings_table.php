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
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receive_no')->unique(); 
            $table->string('supplier_name'); 
            $table->text('supplier_address')->nullable(); 
            $table->string('delivery_order_no'); 
            $table->string('purchase_order_no'); 
            $table->string('item_description'); 
            $table->integer('quantity_ordered'); 
            $table->integer('quantity_received'); 
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
