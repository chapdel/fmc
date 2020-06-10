<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Models\SubscriberImport;

class DestroySubscriberImportController
{
    public function __invoke(SubscriberImport $subscriberImport)
    {
        $subscriberImport->delete();

        flash()->success(__('Import was deleted.'));

        return back();
    }
}
