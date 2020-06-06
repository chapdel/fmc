<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Requests\StoreEmailListRequest;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateEmailListController
{
    use UsesMailcoachModels;

    public function __invoke(StoreEmailListRequest $request)
    {
        $emailList = $this->getEmailListClass()::create([
            'name' => $request->name,
            'requires_confirmation' => true,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'campaign_mailer' => config('mailcoach.campaign_mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
            'transactional_mailer' => config('mailcoach.transactional_mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
        ]);

        flash()->success(__('List :emailList was created', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.subscribers', $emailList);
    }
}
