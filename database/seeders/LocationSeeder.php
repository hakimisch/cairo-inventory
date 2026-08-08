<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seed the locations lookup table.
     *
     * These are the physical spaces within CAIRO where assets are placed.
     * The list includes labs, offices, and common areas across UTM campuses.
     */
    public function run(): void
    {
        $locations = [
            // ── Core Labs ─────────────────────────────────────────────────────
            ['name' => 'Cairo Office',             'type' => 'Office'],
            ['name' => 'Meeting Room',             'type' => 'Common'],
            ['name' => 'Healthcare Lab',           'type' => 'Lab'],
            ['name' => 'Smart Manufacturing Lab',  'type' => 'Lab'],
            ['name' => 'Agriculture Lab',          'type' => 'Lab'],

            // ── Additional Labs & Spaces ──────────────────────────────────────
            ['name' => 'Makmal AI',                'type' => 'Lab'],
            ['name' => 'Makmal Rangkaian',         'type' => 'Lab'],
            ['name' => 'Makmal Robotik',           'type' => 'Lab'],
            ['name' => 'Makmal IoT',               'type' => 'Lab'],
            ['name' => 'Makmal Kejuruteraan',       'type' => 'Lab'],

            // ── Offices & Common ──────────────────────────────────────────────
            ['name' => 'Pejabat',                  'type' => 'Office'],
            ['name' => 'Bilik Pelayan',            'type' => 'Common'],
            ['name' => 'Bilik Rangkaian',          'type' => 'Common'],
            ['name' => 'Stor Utama',               'type' => 'Storage'],
            ['name' => 'Stor Digital',             'type' => 'Storage'],

            // ── Campuses ──────────────────────────────────────────────────────
            ['name' => 'UTM Kuala Lumpur',         'type' => 'Campus'],
            ['name' => 'UTM Johor Bahru',          'type' => 'Campus'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['name' => $loc['name']],
                ['type' => $loc['type']]
            );
        }
    }
}
