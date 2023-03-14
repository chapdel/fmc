<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UploadsController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'file' => ['nullable', 'required_without:url'],
            'url' => ['nullable', 'url', 'required_without:file'],
        ]);

        if (isset($data['file'])) {
            $upload = self::getUploadClass()::create();
            $media = $upload
                ->addMediaFromRequest('file')
                ->sanitizingFileName(fn (string $fileName) => Str::slug($fileName))
                ->toMediaCollection(
                    config('mailcoach.uploads.collection_name', 'default'),
                    config('mailcoach.uploads.disk_name'),
                );
        }

        if (isset($data['url'])) {
            $upload = self::getUploadClass()::create();
            $media = $upload
                ->addMediaFromUrl($data['url'])
                ->toMediaCollection(
                    config('mailcoach.uploads.collection_name', 'default'),
                    config('mailcoach.uploads.disk_name'),
                );
        }

        if (! isset($media)) {
            return response()->json([
                'success' => 0,
            ]);
        }

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => $media->getFullUrl('image'),
            ],
        ]);
    }
}
