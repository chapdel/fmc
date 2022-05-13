<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\EmailLists;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class CreateEmailListAction
{
    public function execute(EmailList $emailList, array $data): EmailList
    {
        $emailList->fill([
            'name' => $data['name'],
            'default_from_email' => $data['default_from_email'],
            'default_from_name' => $data['default_from_name'] ?? null,
            'campaign_mailer' => $data['campaign_mailer'] ?? config('mailcoach.campaigns.mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
            'automation_mailer' => $data['campaign_mailer'] ?? config('mailcoach.campaigns.mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
            'transactional_mailer' => $data['transactional_mailer'] ?? config('mailcoach.transactional.mailer') ?? config('mailcoach.mailer') ?? config('mail.default'),
        ]);

        $emailList->save();

        return $emailList->refresh();
    }
}
