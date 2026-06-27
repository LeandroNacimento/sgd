<?php

namespace Database\Factories;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign the Administrator role.
     */
    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', RoleEnum::Administrator->value)->sole()->id,
        ]);
    }

    /**
     * Assign the Operator role.
     */
    public function asOperator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', RoleEnum::Operator->value)->sole()->id,
        ]);
    }

    /**
     * Assign the Viewer role.
     */
    public function asViewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', RoleEnum::Viewer->value)->sole()->id,
        ]);
    }
}
