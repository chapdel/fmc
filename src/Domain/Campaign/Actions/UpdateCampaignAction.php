<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, array $attributes, Template $template = null): Campaign
    {
        $segment = $attributes['segment_id'] ?? null
            ? TagSegment::find($attributes['segment_id'])
            : null;

        if (is_null($segment)) {
            $segmentClass = $attributes['segment_class'] ?? EverySubscriberSegment::class;
            $segmentDescription = (new $segmentClass)->description();
        } else {
            $segmentClass = $segment::class;
            $segmentDescription = $segment->description($campaign);
        }

        $campaign->fill([
            'name' => $attributes['name'],
            'status' => CampaignStatus::Draft,
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $attributes['html'] ?? $template?->html,
            'template_id' => $template?->id,
            'structured_html' => $attributes['structured_html'] ?? $template?->structured_html,
            'track_opens' => $attributes['track_opens'] ?? config('mailcoach.campaigns.default_settings.track_opens', false),
            'track_clicks' => $attributes['track_clicks'] ?? config('mailcoach.campaigns.default_settings.track_clicks', false),
            'utm_tags' => $attributes['utm_tags'] ?? config('mailcoach.campaigns.default_settings.utm_tags', false),
            'last_modified_at' => now(),
            'email_list_id' => $attributes['email_list_id'] ?? self::getEmailListClass()::orderBy('name')->first()?->id,
            'segment_class' => $segmentClass,
            'segment_description' => $segmentDescription,
            'scheduled_at' => $attributes['schedule_at'] ?? null,
        ]);

        $campaign->save();

        return $campaign->refresh();
    }
}
