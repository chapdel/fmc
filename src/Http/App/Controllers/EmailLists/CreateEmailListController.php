<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Requests\StoreEmailListRequest;
use Spatie\Mailcoach\Models\EmailList;

class CreateEmailListController
{
    public function __invoke(StoreEmailListRequest $request)
    {
        $emailList = EmailList::create([
            'name' => $request->name,
            'requires_confirmation' => true,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'campaign_mailer' => config('mailcoach.campaign_mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
            'transactional_mailer' => config('mailcoach.transactional_mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
        ]);

        flash()->success("List {$emailList->name} was created.");

        return redirect()->route('mailcoach.emailLists.subscribers', $emailList);
    }
}
