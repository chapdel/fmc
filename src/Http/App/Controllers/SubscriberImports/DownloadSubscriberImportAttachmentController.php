<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\SimpleExcel\SimpleExcelWriter;

class DownloadSubscriberImportAttachmentController
{
    use UsesMailcoachModels;

    public function __invoke(SubscriberImport $subscriberImport, string $collection)
    {
        if ($collection === 'errorReport') {
            SimpleExcelWriter::streamDownload('errorReport.csv')
                ->noHeaderRow()
                ->addRows($subscriberImport->errors ?? [])
                ->toBrowser();
        }

        abort_unless((bool)$subscriberImport->getMediaCollection($collection), 403);

        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport->id);

        return $subscriberImport->getFirstMedia($collection);
    }
}
