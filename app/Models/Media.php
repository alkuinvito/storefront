<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['name', 'path', 'size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
