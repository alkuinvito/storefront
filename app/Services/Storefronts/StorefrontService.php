<?php

namespace App\Services\Storefronts;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\Storefront;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class StorefrontService
{
    public function __construct() {}

    public function createStorefront(User $user, array $data)
    {
        $created =  $user->storefronts()->create($data);
        $this->syncStorefront('create');

        return $created;
    }

    public function deleteStorefront(Storefront $storefront)
    {
        $storefront->delete();
        $this->syncStorefront('delete');
    }

    public function getPublicStorefronts()
    {
        return Storefront::where('is_published', true)->get();
    }

    public function getPublicStorefrontBySlug(string $slug)
    {
        return Storefront::where('slug', $slug)->where('is_published', true)->with(['pageblocks' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('index', 'asc');
        }])->first();
    }

    public function getStorefrontById(int $id)
    {
        return Storefront::find($id)->with('pageblocks')->first();
    }

    public function getStorefrontBySlug(string $slug)
    {
        return Storefront::where('slug', $slug)->with(['pageblocks' => function ($query) {
            $query->orderBy('index', 'asc');
        }])->first();
    }

    public function getStorefronts(User $user)
    {
        return $user->storefronts()->paginate(15);
    }

    public function syncStorefront(string $action, ?string $entity = null)
    {
        $url = env('SF_WEBHOOK_HOST') . '/api/webhook';
        $payload = [
            'action' => $action,
            'entity' => $entity
        ];
        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, env('SF_WEBHOOK_SECRET'));

        $response = Http::post($url, ['data' => $jsonPayload, 'signature' => $signature]);

        if ($response->failed()) {
            throw new Exception($response->body());
        }
    }

    public function updateStorefront(Request $request, Storefront $storefront)
    {
        $storefront->update($request->only(['title', 'theme', 'is_published']));
        $this->syncStorefront('update', $storefront->slug);

        return $storefront;
    }
}
