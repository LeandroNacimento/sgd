<?php

namespace App\Models;

use Database\Factories\DocumentStateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentState extends Model
{
    /** @use HasFactory<DocumentStateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function documentVersions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }
}
