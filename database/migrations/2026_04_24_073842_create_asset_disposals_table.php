<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-17/18/19 — Pelupusan (Disposal Workflow)
     * disposal_method enum: Tanam | Bakar | Tenggelam | Jualan | Pindahan
     */
    public function up(): void
    {
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->text('request_reason');
            $table->string('committee_decision')->nullable();
            $table->string('disposal_method')->nullable(); // Tanam, Bakar, Tenggelam, Jualan, Pindahan
            $table->date('disposal_date')->nullable();
            $table->string('approval_reference')->nullable();
            $table->string('status')->default('draft'); // draft | committee_review | approved | completed | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
    }
};
