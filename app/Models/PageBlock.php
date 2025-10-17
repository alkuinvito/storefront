<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    protected $fillable = ['type', 'index', 'content', 'is_active'];

    protected $casts = ['content' => 'array', 'is_active' => 'boolean'];

    /**
     * Get block's parent.
     */
    public function storefront(): BelongsTo
    {
        return $this->belongsTo(Storefront::class);
    }
}
