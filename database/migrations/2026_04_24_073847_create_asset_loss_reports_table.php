<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-28→32 — Kehilangan & Hapuskira (Loss Investigation Workflow)
     * PA-30: investigation_summary
     * PA-31: approval_reference (police report / approval letter)
     * PA-32: action_type (surcharge | write_off)
     */
    public function up(): void
    {
        Schema::create('asset_loss_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('incident_location');
            $table->date('loss_date');
            $table->string('loss_method'); // e.g., 'hilang', 'curi', 'musnah', 'other'
            $table->string('last_officer')->nullable();
            $table->string('police_report_no')->nullable();
            $table->decimal('current_value', 12, 2)->default(0);
            $table->string('action_type')->nullable(); // 'surcharge' | 'write_off'
            $table->decimal('write_off_value', 12, 2)->nullable();
            $table->decimal('surcharge_amount', 12, 2)->nullable();
            $table->date('applied_date')->nullable();
            $table->text('investigation_summary')->nullable(); // PA-30
            $table->string('approval_reference')->nullable();  // PA-31
            $table->string('status')->default('under_investigation'); // under_investigation | committee_review | approved | closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_loss_reports');
    }
};
