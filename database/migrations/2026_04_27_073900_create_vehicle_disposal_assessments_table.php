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
        Schema::create('vehicle_disposal_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('plate_no');                          // Vehicle registration number
            $table->string('chassis_no')->nullable();            // VIN / chassis number
            $table->string('engine_no')->nullable();             // Engine number
            $table->string('vehicle_brand')->nullable();         // Make (Toyota, Proton, etc.)
            $table->string('vehicle_model')->nullable();         // Model
            $table->year('vehicle_year')->nullable();            // Year of manufacture
            $table->date('road_tax_expiry')->nullable();         // Road tax expiry
            $table->string('engine_capacity')->nullable();       // e.g. "2000cc"
            $table->string('fuel_type')->nullable();             // Petrol / Diesel / Electric
            $table->string('vehicle_color')->nullable();         // Colour
            $table->text('condition_report')->nullable();        // Physical condition
            $table->decimal('estimated_value', 12, 2)->nullable(); // Estimated market value
            $table->date('assessment_date')->nullable();         // Date of assessment
            $table->string('assessor_name')->nullable();         // Assessing officer name
            $table->string('assessor_position')->nullable();     // Assessing officer position
            $table->text('recommendation')->nullable();          // Disposal recommendation
            $table->string('status')->default('draft');          // draft / submitted / approved / rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_disposal_assessments');
    }
};
