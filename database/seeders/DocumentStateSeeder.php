<?php

namespace Database\Seeders;

use App\Enums\DocumentStateName;
use App\Models\DocumentState;
use Illuminate\Database\Seeder;

class DocumentStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DocumentStateName::cases() as $state) {
            DocumentState::firstOrCreate(['name' => $state->value]);
        }
    }
}
