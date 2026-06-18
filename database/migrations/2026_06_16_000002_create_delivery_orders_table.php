<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create the delivery_orders table - stores DO document metadata
     * for Phase 2 DO-based item verification system.
     */
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_no');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->date('ack_date')->nullable();
            $table->string('po_reference')->nullable();
            $table->string('sales_rep')->nullable();
            $table->string('terms')->nullable();
            $table->integer('total_pages')->default(1);
            $table->string('status')->default('pending'); // pending | partial | complete | verified
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
