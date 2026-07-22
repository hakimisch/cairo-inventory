<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add bundle/component support to master_items.
     *
     * Some tender line items are bundles that include multiple distinct
     * accessories (mice, keyboards, headphones, bags, etc.) delivered
     * alongside the main equipment. These need separate tracking as
     * sub-components linked to their parent item.
     *
     * Also add a location column to capture which lab/area each item
     * is allocated to (e.g. "Smart Manufacturing Lab").
     */
    public function up(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            // ── Bundle support ──────────────────────────────────────────────
            $table->foreignId('parent_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('master_items')
                  ->nullOnDelete();

            $table->boolean('is_bundle_component')
                  ->default(false)
                  ->after('parent_item_id');

            // ── Location ────────────────────────────────────────────────────
            $table->string('location')
                  ->nullable()
                  ->after('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            $table->dropForeign(['parent_item_id']);
            $table->dropColumn(['parent_item_id', 'is_bundle_component', 'location']);
        });
    }
};
