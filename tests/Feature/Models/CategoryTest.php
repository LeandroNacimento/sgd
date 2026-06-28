<?php

use App\Models\Category;
use App\Models\Document;

test('it can be created', function () {
    $category = Category::factory()->create(['name' => 'HR']);
    expect($category->name)->toBe('HR');
    $this->assertDatabaseHas('categories', ['name' => 'HR']);
});

test('it soft deletes', function () {
    $category = Category::factory()->create();
    $category->delete();

    $this->assertSoftDeleted($category);
});

test('it has many documents', function () {
    $category = Category::factory()->create();

    Document::factory()->count(3)->create([
        'category_id' => $category->id,
    ]);

    expect($category->documents)->toHaveCount(3);
});
