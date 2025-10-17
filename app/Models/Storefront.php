<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storefront extends Model
{
    use HasFactory;

    protected $fillable = ['subdomain', 'title', 'theme', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    /**
     * Get storefront's owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get pageblocks
     */
    public function pageBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class);
    }
}
