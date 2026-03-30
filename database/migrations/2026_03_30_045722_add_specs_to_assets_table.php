<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('model')->nullable()->after('name');
            $table->string('brand')->nullable()->after('model');
            $table->string('serial_number')->nullable()->after('brand');
            $table->boolean('requires_maintenance')->default(false)->after('serial_number');
            $table->string('saga_id')->nullable()->after('warranty_expiry');
            $table->string('voucher_no')->nullable()->after('saga_id');
            $table->string('budget_vot')->nullable()->after('voucher_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            //
        });
    }
};
