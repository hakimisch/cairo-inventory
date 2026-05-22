<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add procedural sale chain fields for PA-21→PA-26:
     * - Tender chain (PA-21→23): sealed envelope, tender box, 12pm closing, 60/90-day validity
     * - Quotation chain (PA-24→26): "no. sebutharga" reference, 60-day validity
     * - Auction chain (PA-27→27A): viewing dates (already has sale_date/location)
     */
    public function up(): void
    {
        Schema::table('disposal_sales', function (Blueprint $table) {
            // Viewing period (all channels)
            $table->date('viewing_date_start')->nullable()->after('sale_location');
            $table->date('viewing_date_end')->nullable()->after('viewing_date_start');

            // Closing deadline (tender & quotation: 12pm noon)
            $table->timestamp('closing_datetime')->nullable()->after('viewing_date_end');

            // Tender-specific
            $table->string('sealed_envelope_ref')->nullable()->after('closing_datetime');
            $table->text('tender_box_address')->nullable()->after('sealed_envelope_ref');

            // Bid validity (60 days local, 90 days international)
            $table->integer('bid_validity_days')->unsigned()->nullable()->after('tender_box_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposal_sales', function (Blueprint $table) {
            $table->dropColumn([
                'viewing_date_start',
                'viewing_date_end',
                'closing_datetime',
                'sealed_envelope_ref',
                'tender_box_address',
                'bid_validity_days',
            ]);
        });
    }
};
