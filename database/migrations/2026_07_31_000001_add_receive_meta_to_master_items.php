<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            if (!Schema::hasColumn('master_items', 'receive_meta')) {
                $table->json('receive_meta')->nullable()->after('invoice_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            if (Schema::hasColumn('master_items', 'receive_meta')) {
                $table->dropColumn('receive_meta');
            }
        });
    }
};
