<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DownloadSubscriberImportAttachmentController
{
    use UsesMailcoachModels;

    public function __invoke(SubscriberImport $subscriberImport, string $collection)
    {
        abort_unless((bool)$subscriberImport->getMediaCollection($collection), 403);

        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport->id);

        return $subscriberImport->getFirstMedia($collection);
    }
}
