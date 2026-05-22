<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * KEW.PA-30 — Laporan Akhir Kehilangan Aset Alih Universiti
     * 8-section investigation report (spans 2 pages, pages 33-34 of KOLEKSI BORANG KEW.PA.pdf)
     * 1:1 relationship with asset_loss_reports
     */
    public function up(): void
    {
        Schema::create('final_loss_reports', function (Blueprint $table) {
            $table->id();

            // 1:1 FK to asset_loss_reports
            $table->foreignId('asset_loss_report_id')
                  ->unique()
                  ->constrained()
                  ->cascadeOnDelete();

            // ─── Section 1: Butiran Aset / Asset Details (a-f) ───
            $table->string('asset_tag_no')->nullable();
            $table->string('asset_description')->nullable();
            $table->string('asset_category')->nullable();
            $table->string('asset_serial_no')->nullable();
            $table->string('asset_location_registered')->nullable();
            $table->string('last_custodian')->nullable();

            // ─── Section 2: Perihal Kehilangan / Loss Description (a-e) ───
            $table->text('incident_description')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('incident_time')->nullable();
            $table->string('incident_location_details')->nullable();
            $table->string('incident_circumstances')->nullable();

            // ─── Section 3: Dapatan Polis / Police Findings ───
            $table->text('police_investigation_findings')->nullable();
            $table->string('police_officer_name')->nullable();
            $table->string('police_station')->nullable();
            $table->string('police_report_ref')->nullable();

            // ─── Section 4: Keterangan Saksi / Witness Statements (a-b) ───
            $table->text('witness_1_statement')->nullable();
            $table->string('witness_1_name')->nullable();
            $table->text('witness_2_statement')->nullable();
            $table->string('witness_2_name')->nullable();

            // ─── Section 5: Pematuhan Prosedur / Procedural Compliance ───
            $table->boolean('complied_with_procedures')->nullable();
            $table->text('procedural_compliance_notes')->nullable();
            $table->text('procedural_gaps')->nullable();

            // ─── Section 6: Langkah Pencegahan / Prevention Steps ───
            $table->text('prevention_actions_taken')->nullable();
            $table->text('prevention_recommendations')->nullable();

            // ─── Section 7: Rumusan Siasatan / Investigation Summary ───
            $table->text('investigation_conclusion')->nullable();

            // ─── Section 8: Syor / Recommendation ───
            // Options: gantian_setara, surcaj, tatatertib, hapuskira
            $table->string('recommended_action')->nullable();
            $table->text('recommendation_rationale')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_loss_reports');
    }
};
