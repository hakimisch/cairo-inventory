<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_sale_item_id')->constrained()->cascadeOnDelete();
            $table->string('bidder_name');                        // Name of bidder
            $table->string('bidder_ic')->nullable();              // IC/Passport number
            $table->string('bidder_phone')->nullable();           // Phone number
            $table->text('bidder_address')->nullable();           // Address
            $table->decimal('bid_amount', 12, 2);                 // Bid amount
            $table->timestamp('bid_date')->nullable();            // Date/time of bid
            $table->boolean('deposit_paid')->default(false);      // Deposit paid?
            $table->decimal('deposit_amount', 12, 2)->nullable(); // Deposit amount
            $table->boolean('is_winner')->default(false);         // Winning bid?
            $table->date('award_date')->nullable();               // Date of award
            $table->date('payment_date')->nullable();             // Date of payment
            $table->boolean('payment_received')->default(false);  // Payment received?
            $table->string('status')->default('pending');         // Pending / Accepted / Rejected / Paid / Completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_bids');
    }
};
