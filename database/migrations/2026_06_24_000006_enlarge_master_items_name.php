<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change master_items.name from varchar(255) to text to accommodate
     * longer OCR-extracted descriptions without SQL truncation errors.
     */
    public function up(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            $table->text('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('master_items', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });
    }
};
