<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the master_items table — canonical 156-item equipment register
     * sourced from the master Excel (Senarai_Peralatan_CAIRO_Lengkap_Subtotal).
     *
     * This is THE authoritative list of all items across all 5 labs,
     * including both "Diterima" (received) and "Belum Diterima" items.
     * DO line items and assets link back here via master_item_id.
     */
    public function up(): void
    {
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();

            // ── Hierarchical identifiers ──────────────────────────────────────
            $table->string('item_no');                         // e.g. "2.1.1.1"
            $table->string('category');                        // Office, Agriculture Lab, etc.
            $table->string('name');                            // Nama Item

            // ── Specs & branding ──────────────────────────────────────────────
            $table->text('specification')->nullable();         // Full tender specification
            $table->string('brand')->nullable();               // Jenama
            $table->text('serial_numbers')->nullable();        // Comma-separated S/Ns

            // ── Pricing & procurement ─────────────────────────────────────────
            $table->string('country_of_origin')->nullable();   // Negara Asal Alat
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->string('sodo_bayaran')->nullable();        // SODO payment code
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            // ── Delivery & status ─────────────────────────────────────────────
            $table->integer('quantity')->default(1);
            $table->string('status')->default('belum_diterima'); // diterima | belum_diterima
            $table->date('received_date')->nullable();
            $table->string('do_no')->nullable();               // Reference DO number

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};
