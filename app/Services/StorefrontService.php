<?php

namespace App\Services;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\Storefront;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StorefrontService
{
    public function __construct() {}

    public function createStorefront(User $user, array $data)
    {
        return $user->storefronts()->create($data);
    }

    public function deleteStorefront(User $user, string $id)
    {
        try {
            $storefront = $user->storefronts()->where('id', (int)$id)->firstOrFail();
            $storefront->delete();
        } catch (ModelNotFoundException) {
            throw new ApiException(ApiErrorCode::ErrNotFound, 'Storefront not found', 404);
        }
    }

    public function getStorefrontById(string $id)
    {
        try {
            return Storefront::findOrFail((int)$id);
        } catch (ModelNotFoundException) {
            throw new ApiException(ApiErrorCode::ErrNotFound, 'Storefront not found', 404);
        }
    }

    public function getStorefronts(User $user)
    {
        return $user->storefronts()->paginate(15);
    }

    public function updateStorefront(User $user, string $id, array $data)
    {
        try {
            $storefront = $user->storefronts()->where('id', (int)$id)->firstOrFail();
            $storefront->update($data);

            return $storefront;
        } catch (ModelNotFoundException) {
            throw new ApiException(ApiErrorCode::ErrNotFound, 'Storefront not found', 404);
        }
    }
}
