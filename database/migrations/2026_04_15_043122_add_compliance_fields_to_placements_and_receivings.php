<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_placements', function (Blueprint $table) {
            $table->string('staff_id')->nullable()->after('custodian_name');
            $table->integer('quantity_placed')->default(1)->after('location');
            $table->string('specific_serial_no')->nullable()->after('quantity_placed');
        });

        Schema::table('receivings', function (Blueprint $table) {
            $table->text('damage_description')->nullable()->after('quantity_received');
            $table->text('notes')->nullable()->after('damage_description');
        });
    }

    public function down(): void
    {
        Schema::table('asset_placements', function (Blueprint $table) {
            $table->dropColumn(['staff_id', 'quantity_placed', 'specific_serial_no']);
        });

        Schema::table('receivings', function (Blueprint $table) {
            $table->dropColumn(['damage_description', 'notes']);
        });
    }
};