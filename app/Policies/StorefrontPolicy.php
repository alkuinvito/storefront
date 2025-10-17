<?php

namespace App\Policies;

use App\Models\Storefront;
use App\Models\User;

class StorefrontPolicy
{
    public function manage(User $user, Storefront $storefront): bool
    {
        return $user->id === $storefront->user_id;
    }
}
