<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupMasterItems extends Command
{
    /**
     * Remove duplicate master_items (same item_no), re-pointing
     * assets/receivings to the kept row.
     *
     * Usage:
     *   php artisan cairo:dedup-master-items
     *   php artisan cairo:dedup-master-items --dry-run   (list duplicates only)
     */
    protected $signature = 'cairo:dedup-master-items
                           {--dry-run : List duplicates without deleting}
                           {--force : Skip the confirmation prompt}';

    protected $description = 'Remove duplicate master_items (same item_no), re-pointing assets/receivings to the kept row.';

    public function handle(): int
    {
        $dupes = DB::table('master_items')
            ->select('item_no', DB::raw('COUNT(*) as cnt'))
            ->groupBy('item_no')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($dupes->isEmpty()) {
            $this->info('No duplicate item_no found. Master list is clean.');
            return Command::SUCCESS;
        }

        $this->table(
            ['item_no', 'duplicates'],
            $dupes->map(fn ($d) => [$d->item_no, $d->cnt])->values()
        );

        if ($this->option('dry-run')) {
            $this->info('DRY RUN — no changes made. Run without --dry-run to deduplicate.');
            return Command::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Delete duplicate master_items (keeping the most-advanced row per item_no) and re-point assets/receivings? This cannot be undone.')) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $deleted = 0;
        $repointed = 0;

        foreach ($dupes as $dup) {
            $rows = DB::table('master_items')
                ->where('item_no', $dup->item_no)
                ->orderBy('status', 'desc')  // keep most-advanced status (verified > diterima > belum_diterima)
                ->orderBy('id', 'desc')
                ->get();

            $keep = $rows->shift();
            foreach ($rows as $row) {
                $repointed += DB::table('assets')->where('master_item_id', $row->id)->update(['master_item_id' => $keep->id]);
                $repointed += DB::table('receivings')->where('master_item_id', $row->id)->update(['master_item_id' => $keep->id]);
                DB::table('master_items')->where('id', $row->id)->delete();
                $deleted++;
            }
        }

        $this->newline();
        $this->info("✓ Deleted {$deleted} duplicate master_items; re-pointed {$repointed} related records.");

        return Command::SUCCESS;
    }
}
