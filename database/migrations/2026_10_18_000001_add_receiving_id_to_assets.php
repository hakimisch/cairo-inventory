<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Link an asset back to the PA-1 (receiving) that produced it.
 *
 * Without this, the register cannot tell whether a PA-1 has already generated an
 * asset, so `destroyReceiving()` had no way to guard a delete. Assets created by
 * Verify are backfilled through their shared `master_item_id`; historical manual
 * KEW.PA-1A rows cannot be reconstructed reliably and stay unlinked.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'receiving_id')) {
            Schema::table('assets', function (Blueprint $table) {
                // No FK constraint: SQLite cannot add one after table creation and
                // the link is enforced in the controller. Deleting a PA-1 is
                // soft-delete only, so the column never dangles.
                $table->unsignedBigInteger('receiving_id')->nullable()->after('master_item_id')->index();
            });
        }

        DB::table('receivings')
            ->select('id', 'master_item_id')
            ->whereNotNull('master_item_id')
            ->orderBy('id')
            ->get()
            ->each(function ($receiving) {
                DB::table('assets')
                    ->where('master_item_id', $receiving->master_item_id)
                    ->whereNull('receiving_id')
                    ->update(['receiving_id' => $receiving->id]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'receiving_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('receiving_id');
            });
        }
    }
};
