<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Storefront extends Model
{
    protected $fillable = ['user_id', 'subdomain', 'title', 'theme', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    /**
     * Get storefront's owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
