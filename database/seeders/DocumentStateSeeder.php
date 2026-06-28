<?php

namespace Database\Seeders;

use App\Models\DocumentState;
use Illuminate\Database\Seeder;

class DocumentStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = ['Draft', 'In Review', 'Published', 'Archived'];

        foreach ($states as $state) {
            DocumentState::firstOrCreate(['name' => $state]);
        }
    }
}
