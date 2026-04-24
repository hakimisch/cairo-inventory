<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_inspections', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_inspections', 'is_record_complete')) {
                $table->boolean('is_record_complete')->nullable()->after('notes')
                      ->comment('Daftar Lengkap? — KEW.PA-10/PA-11');
            }
            if (!Schema::hasColumn('asset_inspections', 'is_record_updated')) {
                $table->boolean('is_record_updated')->nullable()->after('is_record_complete')
                      ->comment('Daftar Kemaskini? — KEW.PA-10/PA-11');
            }
            if (!Schema::hasColumn('asset_inspections', 'actual_location')) {
                $table->string('actual_location')->nullable()->after('is_record_updated')
                      ->comment('Lokasi Sebenar (vs. Rekod) — KEW.PA-10/PA-11');
            }
            if (!Schema::hasColumn('asset_inspections', 'actual_quantity')) {
                $table->integer('actual_quantity')->nullable()->after('actual_location')
                      ->comment('Kuantiti Sebenar (vs. Rekod) — KEW.PA-11');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_inspections', function (Blueprint $table) {
            $table->dropColumn([
                'is_record_complete',
                'is_record_updated',
                'actual_location',
                'actual_quantity',
            ]);
        });
    }
};
