<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Upload;

class AddUploadToCampaignController
{
    use ValidatesRequests;

    public function __invoke(Campaign $campaign, Request $request)
    {
        $this->validate($request, ['file' => 'required|image']);

        $upload = Upload::create();
        $media = $upload->addMediaFromRequest('file')->toMediaCollection();

        $upload->campaigns()->attach($campaign);

        return response()->json(['url' => $media->getFullUrl('image')]);
    }
}
