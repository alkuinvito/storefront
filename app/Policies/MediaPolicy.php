<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function manage(User $user, Media $media)
    {
        return $user->id === $media->user_id;
    }
}
