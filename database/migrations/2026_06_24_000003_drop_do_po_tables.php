<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove the DO/PO abstraction layer from the schema.
     *
     * Tables dropped:
     *   - do_line_items      (items now live in master_items)
     *   - delivery_orders     (DO concept removed)
     *   - purchase_order_items (already empty)
     *   - purchase_orders     (already empty)
     *
     * Column dropped:
     *   - assets.do_line_item_id (orphaned FK)
     */
    public function up(): void
    {
        // 1. Drop FK from assets pointing to do_line_items
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'do_line_item_id')) {
                $table->dropForeign(['do_line_item_id']);
                $table->dropColumn('do_line_item_id');
            }
        });

        // 2. Drop FKs from scans + purchase_order_items referencing do_line_items
        Schema::table('scans', function (Blueprint $table) {
            if (Schema::hasColumn('scans', 'do_line_item_id')) {
                $table->dropForeign(['do_line_item_id']);
                $table->dropColumn('do_line_item_id');
            }
        });
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'do_line_item_id')) {
                $table->dropForeign(['do_line_item_id']);
                $table->dropColumn('do_line_item_id');
            }
        });

        // 3. Drop line-level tables first (they hold FKs to parent tables)
        Schema::dropIfExists('do_line_items');
        Schema::dropIfExists('purchase_order_items');

        // 4. Drop parent tables
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('purchase_orders');
    }

    public function down(): void
    {
        // ── Restore do_line_item_id on scans ──────────────────────────────────
        Schema::table('scans', function (Blueprint $table) {
            $table->foreignId('do_line_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();
        });

        // ── Recreate purchase_orders ──────────────────────────────────────────
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->decimal('grand_total', 14, 2)->nullable();
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Recreate delivery_orders ──────────────────────────────────────────
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_no');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->date('ack_date')->nullable();
            $table->string('po_reference')->nullable();
            $table->string('sales_rep')->nullable();
            $table->string('terms')->nullable();
            $table->integer('total_pages')->default(1);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Recreate purchase_order_items ─────────────────────────────────────
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_code')->nullable();
            $table->text('description');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('category')->nullable();
            $table->integer('quantity_ordered')->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 14, 2)->nullable();
            $table->decimal('total_price', 14, 2)->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('do_line_item_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->after('supplier_id')
                  ->constrained()->nullOnDelete();
        });

        // ── Recreate do_line_items ────────────────────────────────────────────
        Schema::create('do_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('item_code')->nullable();
            $table->text('description');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_ordered')->default(1);
            $table->integer('quantity_received')->default(0);
            $table->string('unit')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('scan_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Restore do_line_item_id on assets ────────────────────────────────
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('do_line_item_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }
};
