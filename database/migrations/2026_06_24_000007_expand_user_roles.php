<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change from enum('admin', 'user') to string to support more roles
            $table->string('role', 50)->default('user')->change();
        });
    }

    public function down(): void
    {
        // Laravel can't revert string back to enum in a down migration reliably
        // This is a one-way change; to revert, restore from backup
    }
};
