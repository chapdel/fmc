<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Requests\ImportSubscribersRequest;
use Spatie\Mailcoach\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\SubscriberImport;

class ImportSubscribersController
{
    public function showImportScreen(EmailList $emailList)
    {
        return view('mailcoach::app.emailLists.importSubscribers', [
            'emailList' => $emailList,
            'subscriberImports' => $emailList->subscriberImports()->latest()->get(),
        ]);
    }

    public function import(EmailList $emailList, ImportSubscribersRequest $request)
    {
        /** @var \Spatie\Mailcoach\Models\SubscriberImport $subscriberImport */
        $subscriberImport = SubscriberImport::create([
            'email_list_id' => $emailList->id,
        ]);

        $subscriberImport
            ->addMediaFromRequest('file')
            ->toMediaCollection('importFile');

        dispatch(new ImportSubscribersJob($subscriberImport, auth()->user()));

        flash()->success('Your file has been uploaded. Follow the import status in the list below.');

        return redirect()->back();
    }
}
