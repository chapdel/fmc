<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Carbon\Carbon;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

class CampaignFactory
{
    protected int $subscriberCount = 0;

    protected string $mailable = MailcoachMail::class;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function mailable(string $mailable)
    {
        $this->mailable = $mailable;

        return $this;
    }

    public function create(array $attributes = []): Campaign
    {
        $emailList = (new EmailListFactory())
            ->withSubscriberCount($this->subscriberCount)
            ->create([
                'requires_confirmation' => false,
            ]);

        $campaign = Campaign::factory()
            ->create($attributes)
            ->useMailable($this->mailable)
            ->to($emailList);

        return $campaign->refresh();
    }

    public static function createSentAt(string $dateTime): Campaign
    {
        return Campaign::factory()->create([
            'sent_at' => Carbon::createFromFormat('Y-m-d H:i:s', $dateTime),
            'all_sends_dispatched_at' => Carbon::createFromFormat('Y-m-d H:i:s', $dateTime),
            'all_sends_created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $dateTime),
        ]);
    }
}
