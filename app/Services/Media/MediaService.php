<?php

namespace App\Services\Media;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function createMedia(User $user, Request $request)
    {
        $file = $request->file('image');

        $storageLimit = 50_000_000; // 50 MB
        $filesize = $file->getSize();
        $storageUsed = $user->storage_used;
        if ($storageUsed + $filesize > $storageLimit) {
            throw new ApiException(ApiErrorCode::ErrStorageLimitExceeded, null, 422);
        }

        $filename = Storage::putFile('media', $file);
        if (!$filename) {
            throw new ApiException(ApiErrorCode::ErrUploadFailed, null, 500);
        };

        $user->update(['storage_used' => $storageUsed + $filesize]);

        return $user->media()->create(['name' => $filename, 'size' => $filesize]);
    }

    public function getMedia(User $user)
    {
        return $user->media()->paginate(10);
    }
}
