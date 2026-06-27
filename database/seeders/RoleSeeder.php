<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed all fixed domain roles.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $case) {
            Role::firstOrCreate(['name' => $case->value]);
        }
    }
}
