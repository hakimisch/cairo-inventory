<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Safety check to prevent "Duplicate column" error
            if (!Schema::hasColumn('users', 'role')) {
                // 'admin' = full access + reports; 'user' = standard access (default)
                $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};