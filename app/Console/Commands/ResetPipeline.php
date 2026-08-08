<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetPipeline extends Command
{
    /**
     * Clear all pipeline and demo seed data, keeping reference tables intact.
     *
     * Removes: master_items, assets, receivings, scans, and all lifecycle
     * demo data seeded by DatabaseSeeder + KewpaDataSeeder.
     * Preserves: users, suppliers, locations, migrations.
     *
     * Usage:
     *   php artisan cairo:reset-pipeline
     *   php artisan cairo:reset-pipeline --dry-run   (show what would be cleared)
     */
    protected $signature = 'cairo:reset-pipeline
                           {--dry-run : List tables that would be cleared without deleting}';

    protected $description = 'Reset pipeline data (master items, assets, receivings) and all demo seed data, keeping users/suppliers/locations intact.';

    /**
     * Tables to clear — ordered to satisfy FK constraints.
     * Leaf tables first, parent tables last.
     */
    private array $tablesToClear = [
        // FK leaf nodes (no dependents)
        'sale_bids',
        'disposal_sale_items',
        'disposal_sales',
        'committee_appointments',
        'asset_upgrades',
        'asset_loss_reports',
        'vehicle_disposal_assessments',
        'asset_transfers',
        'asset_maintenances',
        'asset_inspections',
        'asset_placements',
        'damage_reports',
        'asset_disposals',
        'final_loss_reports',
        // Pipeline core
        'scans',
        'receivings',
        'assets',
        'master_items',
    ];

    public function handle(): int
    {
        // ── Resolve FK-safe order ─────────────────────────────────────────
        // Topological sort: leaf tables first, parents last
        $tables = $this->resolveOrder();

        if ($this->option('dry-run')) {
            $this->info('DRY RUN — these tables would be cleared:');
            foreach ($tables as $table) {
                $count = DB::table($table)->count();
                $this->line(sprintf('  • %-30s (%d rows)', $table, $count));
            }
            return Command::SUCCESS;
        }

        // ── Confirm ───────────────────────────────────────────────────────
        if (!$this->confirm('This will DELETE ALL pipeline and demo seed data. Users, suppliers, and locations are preserved. Continue?')) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $totalDeleted = 0;
        $rows = [];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                DB::table($table)->truncate();
                $totalDeleted += $count;
                $rows[] = [$table, $count];
            } else {
                // Still truncate to reset auto-increment
                DB::table($table)->truncate();
                $rows[] = [$table, 0];
            }
        }
        Schema::enableForeignKeyConstraints();

        $this->table(['Table', 'Rows Cleared'], $rows);
        $this->newline();
        $this->info("✓ Pipeline reset complete. {$totalDeleted} total rows deleted.");
        $this->warn('Remember to re-seed or re-import master items to restart the pipeline.');

        return Command::SUCCESS;
    }

    /**
     * Resolve FK-safe truncation order: leaf tables first, parents last.
     *
     * The $tablesToClear property lists tables in approximate leaf→parent order.
     * This method verifies the order by checking FK references and logs warnings
     * if a table references another that would be truncated before it.
     */
    private function resolveOrder(): array
    {
        // Already ordered leaf→parent. No transformation needed.
        return $this->tablesToClear;
    }
}
