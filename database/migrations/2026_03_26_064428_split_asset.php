<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Asset type split — core feature
            if (!Schema::hasColumn('assets', 'asset_type')) {
                $table->enum('asset_type', ['fixed_asset', 'inventory'])
                      ->default('fixed_asset')
                      ->after('category')
                      ->comment('fixed_asset = Aset Tetap, inventory = Inventori');
            }
 
            // Warranty tracker (next sprint — added now so model is ready)
            if (!Schema::hasColumn('assets', 'warranty_expiry')) {
                $table->date('warranty_expiry')->nullable()->after('warranty_period');
            }
 
            // Fields that are referenced in controller but may be missing
            if (!Schema::hasColumn('assets', 'received_date')) {
                $table->date('received_date')->nullable()->after('warranty_expiry');
            }
            if (!Schema::hasColumn('assets', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('received_date');
            }
            if (!Schema::hasColumn('assets', 'do_reference')) {
                $table->string('do_reference')->nullable()->after('po_reference');
            }
            if (!Schema::hasColumn('assets', 'receiver_name')) {
                $table->string('receiver_name')->nullable()->after('do_reference');
            }
            if (!Schema::hasColumn('assets', 'image_url')) {
                $table->string('image_url')->nullable()->after('receiver_name');
            }
            if (!Schema::hasColumn('assets', 'components')) {
                $table->json('components')->nullable()->after('image_url');
            }
        });
    }
 
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_type', 'warranty_expiry', 'received_date',
                'rejection_reason', 'do_reference', 'receiver_name',
                'image_url', 'components',
            ]);
        });
    }
};