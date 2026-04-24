<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-13/14 — Penyelenggaraan (Maintenance Tracking)
     */
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->date('maintenance_date');
            $table->text('description');
            $table->string('contract_no')->nullable();
            $table->string('company_name')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending | in_progress | completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
