<?php

namespace Database\Factories;

use App\Enums\DocumentPriority;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'DOC-'.fake()->unique()->numerify('2026-######'),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(DocumentPriority::cases())->value,
            'category_id' => Category::factory(),
            'document_state_id' => DocumentState::factory(),
            'responsible_user_id' => User::factory(),
        ];
    }
}
