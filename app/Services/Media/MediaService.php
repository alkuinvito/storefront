<?php

namespace App\Services\Media;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            throw new ApiException(ApiErrorCode::ErrMediaUploadFailed, null, 500);
        };

        $user->update(['storage_used' => $storageUsed + $filesize]);

        return $user->media()->create(['name' => $filename, 'size' => $filesize]);
    }

    public function deleteMedia(User $user, Media $media)
    {
        if (!Storage::delete($media->name)) {
            throw new ApiException(ApiErrorCode::ErrMediaDeleteFailed, null, 500);
        }

        $user->update(['storage_used' => $user->storage_used - $media->size]);
        $media->delete();
    }

    public function getMedia(User $user)
    {
        return $user->media()->paginate(10);
    }
}
