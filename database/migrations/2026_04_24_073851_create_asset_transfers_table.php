<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-6 — Daftar Pergerakan (Movement/Transfer Register)
     * Tracks asset movement between locations/custodians
     */
    public function up(): void
    {
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('from_location')->nullable();
            $table->string('to_location');
            $table->string('from_custodian')->nullable();
            $table->string('to_custodian');
            $table->date('transfer_date');
            $table->string('reference_no')->nullable(); // DO/reference number
            $table->string('status')->default('pending'); // pending | approved | completed | cancelled
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
