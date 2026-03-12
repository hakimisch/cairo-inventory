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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); // [cite: 6]
            $table->string('name');
            $table->string('category'); // [cite: 11]
            $table->string('national_code')->nullable(); // [cite: 11]
            $table->decimal('purchase_price', 15, 2); // [cite: 11]
            $table->string('location'); // [cite: 12]
            $table->string('status')->default('active'); // [cite: 13]
        
            // Administrative Fields for KEW.PA-3
            $table->string('supplier_name')->nullable(); // [cite: 11]
            $table->text('supplier_address')->nullable(); // [cite: 11]
            $table->string('po_reference')->nullable(); // [cite: 11]
            $table->string('warranty_period')->nullable(); // [cite: 11]
            $table->date('received_date')->nullable(); // [cite: 11]
            $table->timestamps();
        });
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
