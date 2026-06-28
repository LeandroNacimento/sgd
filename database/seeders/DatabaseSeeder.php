<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            DocumentStateSeeder::class,
        ]);

        $adminRole = Role::firstWhere('name', RoleEnum::Administrator->value);

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@sgd.local')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role_id' => $adminRole?->id,
            ]
        );
    }
}
