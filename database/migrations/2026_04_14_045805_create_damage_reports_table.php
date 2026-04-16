<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            
            // Bahagian 1: Pegawai Pelapor
            $table->string('reported_by');
            $table->date('damage_date');
            $table->string('last_user')->nullable();
            $table->decimal('previous_maintenance_cost', 10, 2)->default(0);
            $table->text('damage_description');
            
            // Status tracker
            $table->enum('status', ['pending', 'investigating', 'resolved'])->default('pending');
            
            // Bahagian 2 & 3: Untuk masa hadapan (Admin/Ketua PTJ)
            $table->text('technical_notes')->nullable();
            $table->string('recommendation')->nullable(); // 'repair', 'dispose'
            $table->string('hod_decision')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};