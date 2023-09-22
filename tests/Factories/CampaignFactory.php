<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

class CampaignFactory
{
    protected int $subscriberCount = 0;

    protected string $mailable = MailcoachMail::class;

    public static function new(): self
    {
        return new self;
    }

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
        if ($attributes['email_list_id'] ?? null) {
            $emailList = EmailList::find($attributes['email_list_id']);
        } else {
            $emailList = (new EmailListFactory())
                ->withSubscriberCount($this->subscriberCount)
                ->create([
                    'requires_confirmation' => false,
                ]);
        }

        $attributes['email_list_id'] = $emailList->id;

        $contentAttributes = [
            'html',
            'webview_html',
            'email_html',
            'structured_html',
            'subject',
            'template_id',
            'utm_tags',
            'statistics_calculated_at',
        ];

        $campaign = Campaign::factory()
            ->create(Arr::except($attributes, $contentAttributes))
            ->useMailable($this->mailable);

        $contentItem = ContentItem::factory()->make(Arr::only($attributes, $contentAttributes));

        $campaign->contentItem->update(Arr::except($contentItem->toArray(), ['model_id', 'model_type']));

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
