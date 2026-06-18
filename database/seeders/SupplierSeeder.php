<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::firstOrCreate(['registration_no' => '199901011819 (486719-K)'], [
            'name'             => 'Dotcom Telecommunications Sdn Bhd',
            'registration_no'  => '199901011819 (486719-K)',
            'address'          => 'B-2-7 Endah Promenade, No.5 Jalan 3/149E, Taman Sri Endah, Bandar Baru Sri Petaling, 57000 Kuala Lumpur',
            'phone'            => '03-79832134',
            'email'            => null,
            'contact_person'   => 'Assoc. Professor Ts. Dr. Mohd Ibrahim Shapiai',
            'is_active'        => true,
        ]);

        Supplier::firstOrCreate(['registration_no' => '512056-K'], [
            'name'             => 'SNS Network (M) Sdn Bhd',
            'registration_no'  => '512056-K',
            'address'          => '6-11-2, Block C, 3 Two Square, Jalan 19/1, 46200 Petaling Jaya, Selangor',
            'phone'            => '03-7960 0616',
            'email'            => null,
            'contact_person'   => null,
            'is_active'        => true,
        ]);

        $this->command->info('Seeded 2 suppliers: Dotcom Telecommunications + SNS Network');
    }
}
