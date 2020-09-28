<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\ImportSubscribersRequest;
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
            'subscribe_unsubscribed' => $request->subscribeUnsubscribed(),
            'unsubscribe_others' => $request->unsubscribeMissing(),
        ]);

        $this->addMediaToSubscriberImport($request, $subscriberImport);

        $user = auth()->user();

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null));

        flash()->success(__('Your file has been uploaded. Follow the import status in the list below.'));

        return redirect()->back();
    }

    protected function addMediaToSubscriberImport(
        ImportSubscribersRequest $request,
        SubscriberImport $subscriberImport
    ): void {
        if ($request->has('file')) {
            $subscriberImport
                ->addMediaFromRequest('file')
                ->toMediaCollection('importFile');

            return;
        }

        $subscriberImport
            ->addMediaFromString($request->subscribers_csv)
            ->usingFileName('subscribers.csv')
            ->toMediaCollection('importFile');
    }
}
