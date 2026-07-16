<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add master_item_id to scans table and drop the old do_line_item_id.
     */
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->foreignId('master_item_id')
                  ->nullable()
                  ->after('scanner_user_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_item_id');
        });
    }
};
