<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Returns the translated display label for the category.
     */
    public function label(): string
    {
        $key = 'documents.categories.'.Str::slug($this->name, '_');

        return Lang::has($key) ? __($key) : $this->name;
    }
}
