<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add serial number tracking flags to do_line_items.
     *
     * - has_serial: false if item genuinely has no serial (consumables, tools, etc.)
     * - serial_editable: true if the serial number can be edited after creation
     */
    public function up(): void
    {
        Schema::table('do_line_items', function (Blueprint $table) {
            $table->boolean('has_serial')->default(true)->after('serial_number');
            $table->boolean('serial_editable')->default(true)->after('has_serial');
        });
    }

    public function down(): void
    {
        Schema::table('do_line_items', function (Blueprint $table) {
            $table->dropColumn(['has_serial', 'serial_editable']);
        });
    }
};
