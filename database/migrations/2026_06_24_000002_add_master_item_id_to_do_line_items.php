<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add master_item_id FK to do_line_items, linking each DO line item
     * back to the canonical master equipment register.
     *
     * Existing DO items remain unlinked (master_item_id = null) until
     * a matching/linking command is run.
     */
    public function up(): void
    {
        Schema::table('do_line_items', function (Blueprint $table) {
            $table->foreignId('master_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('do_line_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_item_id');
        });
    }
};
