<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Carbon\Carbon;
use Spatie\Mailcoach\Models\Campaign;

class CampaignFactory
{
    /** @var int */
    private int $subscriberCount;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function create(array $attributes = []): Campaign
    {
        $emailList = (new EmailListFactory())
            ->withSubscriberCount($this->subscriberCount)
            ->create([
                'requires_confirmation' => false,
            ]);

        $campaign = factory(Campaign::class)
            ->create($attributes)
            ->to($emailList);

        return $campaign->refresh();
    }

    public static function createSentAt(string $dateTime): Campaign
    {
        return factory(Campaign::class)->create([
            'sent_at' => Carbon::createFromFormat('Y-m-d H:i:s', $dateTime),
        ]);
    }
}
