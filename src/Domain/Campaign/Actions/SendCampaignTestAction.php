<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignTestAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, string $email): void
    {
        $originalUpdatedAt = $campaign->updated_at;
        $originalSubject = $campaign->subject;
        $campaign->subject = "[Test] {$originalSubject}";

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($campaign);

        if (! $subscriber = self::getSubscriberClass()::where('email', $email)->where('email_list_id', $campaign->email_list_id)->first()) {
            $subscriber = self::getSubscriberClass()::make([
                'uuid' => Str::uuid()->toString(),
                'email_list_id' => $campaign->email_list_id,
                'email' => $email,
            ]);
        }

        $send = self::getSendClass()::make([
            'uuid' => Str::uuid()->toString(),
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
        ]);
        $send->setRelation('subscriber', $subscriber);
        $send->setRelation('campaign', $campaign);

        try {
            /** @var \Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction $sendMailAction */
            $sendMailAction = Mailcoach::getCampaignActionClass('send_mail', SendMailAction::class);
            $sendMailAction->execute($send, isTest: true);
        } finally {
            $campaign->update([
                'subject' => $originalSubject,
                'updated_at' => $originalUpdatedAt,
            ]);
            $send->delete();
        }
    }
}
