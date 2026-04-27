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
        Schema::create('disposal_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_disposal_id')->constrained()->cascadeOnDelete();
            $table->string('sale_type');                          // Tawaran / Sebutharga / Lelongan
            $table->string('sale_reference')->unique();           // Unique sale reference number
            $table->date('sale_date')->nullable();                // Date of sale
            $table->string('sale_location')->nullable();          // Location of sale/auction
            $table->string('sale_officer')->nullable();           // Officer in charge
            $table->text('description')->nullable();              // General description
            $table->text('terms_conditions')->nullable();         // Terms and conditions
            $table->decimal('deposit_required', 12, 2)->nullable(); // Required deposit
            $table->string('sale_status')->default('open');       // Open / Closed / Cancelled / Awarded
            $table->date('decision_date')->nullable();            // PA-24 decision date
            $table->text('decision_notes')->nullable();           // PA-24 decision notes
            $table->date('report_date')->nullable();              // PA-25 report date
            $table->text('report_notes')->nullable();             // PA-25 report notes
            $table->date('certificate_date')->nullable();         // PA-26/27/27A certificate date
            $table->string('certificate_type')->nullable();       // Tawaran / Sebutharga / Lelongan / Pelupusan / Lupus
            $table->string('certificate_reference')->nullable();  // Certificate reference number
            $table->string('status')->default('draft');           // Draft / Active / Completed / Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_sales');
    }
};
