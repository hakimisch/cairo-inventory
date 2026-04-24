<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'quantity')) {
                $table->integer('quantity')->default(1)->after('status')
                      ->comment('Kuantiti — for inventory items (KEW.PA-3)');
            }
            if (!Schema::hasColumn('assets', 'unit_of_measure')) {
                $table->string('unit_of_measure')->default('Unit')->after('quantity')
                      ->comment('Unit Pengukuran — e.g. Unit, Set, Kg (KEW.PA-3)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_of_measure']);
        });
    }
};
