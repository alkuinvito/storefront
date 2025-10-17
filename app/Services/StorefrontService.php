<?php

namespace App\Services;

use App\Models\Storefront;
use App\Models\User;
use Illuminate\Http\Request;

class StorefrontService
{
    public function __construct() {}

    public function createStorefront(User $user, array $data)
    {
        return $user->storefronts()->create($data);
    }

    public function deleteStorefront(Storefront $storefront)
    {
        $storefront->delete();
    }

    public function getStorefronts(User $user)
    {
        return $user->storefronts()->paginate(15);
    }

    public function updateStorefront(Request $request, Storefront $storefront)
    {
        $storefront->update($request->only(['title', 'theme', 'is_published']));
        return $storefront;
    }
}
