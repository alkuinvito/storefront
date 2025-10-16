<?php

namespace App\Http\Controllers\Storefronts;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\StorefrontService;

class StorefrontApiController extends Controller
{
    public function __construct(protected StorefrontService $storefrontService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized);
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
            'subdomain' => 'required|string|unique:storefronts,subdomain|regex:/^[a-zA-Z0-9-]{3,50}$/',
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

        $this->storefrontService->createStorefront($user, $request->only(['subdomain', 'title', 'theme']));

        return response()->json(['data' => 'Storefront created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $storefront = $this->storefrontService->getStorefrontById($id);

        return response()->json(['data' => $storefront]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string',
            'is_published' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $user = Auth::user();

        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized);
        }

        $this->storefrontService->updateStorefront($user, $id, $request->only(['title', 'theme', 'is_published']));

        return response()->json(['data' => 'Storefront updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized);
        }

        $this->storefrontService->deleteStorefront($user, $id);

        return response()->json(['data' => 'Storefront deleted successfully'], 200);
    }
}
