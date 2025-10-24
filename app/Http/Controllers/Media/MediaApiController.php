<?php

namespace App\Http\Controllers\Media;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Illuminate\Log\log;

class MediaApiController extends Controller
{

    public function __construct(protected MediaService $mediaService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $media = $this->mediaService->getMedia($user);

        return response()->json(['data' => $media]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized, null, 401);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $media = $this->mediaService->createMedia($user, $request);

        return response()->json($media, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $medium)
    {
        $user = Auth::user();

        if ($user == null) {
            throw new ApiException(ApiErrorCode::ErrUnauthorized, null, 401);
        }

        if ($user->id != $medium->user_id) {
            throw new ApiException(ApiErrorCode::ErrForbidden, null, 403);
        }

        $this->mediaService->deleteMedia($user, $medium);

        return response()->json(['message' => 'Media deleted successfully'], 200);
    }
}
