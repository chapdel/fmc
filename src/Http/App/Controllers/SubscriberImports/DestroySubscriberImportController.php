<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DestroySubscriberImportController
{
    use UsesMailcoachModels;

    public function __invoke(SubscriberImport $subscriberImport)
    {
        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport->id);
        $subscriberImport->delete();

        flash()->success(__('mailcoach - Import was deleted.'));

        return back();
    }
}
