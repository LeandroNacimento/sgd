<?php

namespace Database\Factories;

use App\Enums\DocumentPriority;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'DOC-'.fake()->unique()->numerify('2026-######'),
            'priority' => fake()->randomElement(DocumentPriority::cases())->value,
            'category_id' => Category::factory(),
            'responsible_user_id' => User::factory(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Document $document) {
            if (! $document->current_version_id) {
                // Read properties passed to the document to propagate to version
                $version = DocumentVersion::factory()->create([
                    'document_id' => $document->id,
                    'title' => $document->_temp_title ?? fake()->sentence(),
                    'description' => $document->_temp_description ?? fake()->paragraph(),
                    'document_state_id' => $document->_temp_document_state_id ?? DocumentState::factory(),
                ]);

                $document->update(['current_version_id' => $version->id]);
            }
        });
    }
}
