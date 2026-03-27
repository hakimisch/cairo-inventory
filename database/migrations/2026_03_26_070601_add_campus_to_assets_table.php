<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'campus')) {
                $table->enum('campus', ['utm_kl', 'utm_jb', 'other'])
                      ->default('utm_jb')
                      ->after('location')
                      ->comment('utm_kl = UTM Kuala Lumpur, utm_jb = UTM Johor Bahru');
            }
        });
    }
 
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('campus');
        });
    }
};
 