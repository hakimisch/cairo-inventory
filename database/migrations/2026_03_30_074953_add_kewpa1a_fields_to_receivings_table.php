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
        Schema::table('receivings', function (Blueprint $table) {
            //
                        if (!Schema::hasColumn('receivings', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('delivery_order_no')
                      ->comment('No Invois — shown in KEW.PA-1A header');
            }
            // Pricing — needed to auto-classify asset type on acceptance
            if (!Schema::hasColumn('receivings', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->default(0)->after('quantity_received');
            }
            if (!Schema::hasColumn('receivings', 'total_price')) {
                $table->decimal('total_price', 12, 2)->default(0)->after('unit_price');
            }
            // Signature block — Pegawai Penerima
            if (!Schema::hasColumn('receivings', 'receiver_name')) {
                $table->string('receiver_name')->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('receivings', 'receiver_position')) {
                $table->string('receiver_position')->nullable()->after('receiver_name');
            }
            // Signature block — Pegawai Bertanggungjawab (Technical Officer)
            if (!Schema::hasColumn('receivings', 'technical_officer_name')) {
                $table->string('technical_officer_name')->nullable()->after('receiver_position');
            }
            if (!Schema::hasColumn('receivings', 'technical_officer_position')) {
                $table->string('technical_officer_position')->nullable()->after('technical_officer_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            //
                        $table->dropColumn([
                'invoice_no', 'unit_price', 'total_price',
                'receiver_name', 'receiver_position',
                'technical_officer_name', 'technical_officer_position',
            ]);

        });
    }
};
