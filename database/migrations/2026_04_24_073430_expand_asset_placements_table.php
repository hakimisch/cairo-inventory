<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_placements', function (Blueprint $table) {
            // PA-9A Loan Form fields
            if (!Schema::hasColumn('asset_placements', 'borrower_phone')) {
                $table->string('borrower_phone')->nullable()->after('staff_id')
                      ->comment('No. Tel. Bimbit Peminjam — KEW.PA-9A');
            }
            if (!Schema::hasColumn('asset_placements', 'matric_no')) {
                $table->string('matric_no')->nullable()->after('borrower_phone')
                      ->comment('No. Staf/Matrik Peminjam — KEW.PA-9A');
            }
            if (!Schema::hasColumn('asset_placements', 'authorizer_name')) {
                $table->string('authorizer_name')->nullable()->after('matric_no')
                      ->comment('Maklumat Penyerah/Pemberi Pinjam Alatan — KEW.PA-9A');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_placements', function (Blueprint $table) {
            $table->dropColumn([
                'borrower_phone',
                'matric_no',
                'authorizer_name',
            ]);
        });
    }
};
