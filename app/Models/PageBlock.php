<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    protected $fillable = ['storefront_id', 'type', 'content', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Get block's parent.
     */
    public function storefront(): BelongsTo
    {
        return $this->belongsTo(Storefront::class);
    }
}
