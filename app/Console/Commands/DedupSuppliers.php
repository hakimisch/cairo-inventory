<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupSuppliers extends Command
{
    /**
     * Merge duplicate supplier records into their canonical rows and
     * re-point master_items at the survivors.
     *
     * Canonical map (from the import history):
     *   Dotcom → keep ID 1, merge 6 (abbreviated) and 7 (DOTCOM … Sdn. Bhd.)
     *   SNS    → keep ID 3, merge 8 (SNS Network (M) Sdn. Bhd.)
     *
     * Usage:
     *   php artisan cairo:dedup-suppliers
     *   php artisan cairo:dedup-suppliers --dry-run   (show what would merge)
     */
    protected $signature = 'cairo:dedup-suppliers
                           {--dry-run : Show what would be merged without changing data}
                           {--force : Skip the confirmation prompt}';

    protected $description = 'Merge duplicate suppliers into canonical rows (Dotcom → ID 1, SNS → ID 3) and re-point master_items.';

    /**
     * fromId => canonical toId
     */
    private array $map = [
        6 => 1, // Dotcom (abbreviated) → Dotcom
        7 => 1, // DOTCOM Telecommunications Sdn. Bhd. → Dotcom
        8 => 3, // SNS Network (M) Sdn. Bhd. → SNS
    ];

    public function handle(): int
    {
        $rows = [];
        foreach ($this->map as $fromId => $toId) {
            $from = DB::table('suppliers')->where('id', $fromId)->first();
            $to   = DB::table('suppliers')->where('id', $toId)->first();

            if (!$from) {
                $this->warn("Supplier {$fromId} not found — skipped.");
                continue;
            }
            if (!$to) {
                $this->warn("Canonical supplier {$toId} not found — cannot merge {$fromId}.");
                continue;
            }

            $affected = DB::table('master_items')->where('supplier_id', $fromId)->count();
            $rows[] = [$fromId, $from->name, '→', $toId, $to->name, $affected];
        }

        if (empty($rows)) {
            $this->info('Nothing to deduplicate.');
            return Command::SUCCESS;
        }

        $this->table(['From ID', 'From Name', '', 'To ID', 'To Name', 'master_items'], $rows);

        if ($this->option('dry-run')) {
            $this->info('DRY RUN — no changes made. Run without --dry-run to merge.');
            return Command::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Merge the suppliers above and re-point master_items? This cannot be undone.')) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $updated = 0;
        $deleted = 0;

        foreach ($this->map as $fromId => $toId) {
            $from = DB::table('suppliers')->where('id', $fromId)->first();
            if (!$from) {
                continue;
            }

            $updated += DB::table('master_items')->where('supplier_id', $fromId)->update(['supplier_id' => $toId]);
            DB::table('suppliers')->where('id', $fromId)->delete();
            $deleted++;
        }

        $this->newline();
        $this->info("✓ Merged {$deleted} duplicate supplier(s); re-pointed {$updated} master_items.");
        $this->info('Remaining suppliers: ' . DB::table('suppliers')->count());

        return Command::SUCCESS;
    }
}
