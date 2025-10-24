<?php

namespace App\Http\Controllers\Storefronts;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Storefront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Storefronts\StorefrontService;

class StorefrontApiController extends Controller
{
    public function __construct(protected StorefrontService $storefrontService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        $isGuest = $user == null;

        if ($isGuest) {
            $storefronts = $this->storefrontService->getPublicStorefronts();
            return response()->json(['data' => $storefronts]);
        }

        $storefronts = $this->storefrontService->getStorefronts($user);

        return response()->json($storefronts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|unique:storefronts,slug|regex:/^[a-zA-Z0-9-]{3,50}$/',
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $user = Auth::user();

        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized);
        }

        $this->storefrontService->createStorefront($user, $request->only(['slug', 'title', 'theme']));

        return response()->json(['data' => 'Storefront created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Storefront $storefront)
    {
        $user = Auth::guard('sanctum')->user();
        $storefront = $this->storefrontService->getPublicStorefrontBySlug($storefront->slug);
        $isAuthorized = $user != null && $storefront->user_id == $user?->id;

        if (!$isAuthorized) {
            return response()->json(['data' => $storefront]);
        }

        $storefront = $this->storefrontService->getStorefrontBySlug($storefront->slug);
        return response()->json(['data' => $storefront]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Storefront $storefront)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string',
            'is_published' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $this->storefrontService->updateStorefront($request, $storefront);

        return response()->json(['data' => 'Storefront updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Storefront $storefront)
    {
        $this->storefrontService->deleteStorefront($storefront);

        return response()->json(['data' => 'Storefront deleted successfully'], 200);
    }
}
