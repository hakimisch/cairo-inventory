<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-15/29 — Pelantikan Jawatankuasa (Committee Appointment)
     * Polymorphic: can be linked to disposal (PA-15) or loss investigation (PA-29)
     */
    public function up(): void
    {
        Schema::create('committee_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('appointable'); // appointable_type + appointable_id
            $table->string('role'); // e.g., 'chairman', 'member', 'secretary'
            $table->date('valid_from');
            $table->date('valid_until');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_appointments');
    }
};
