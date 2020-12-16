<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class UpdateCampaignAction
{
    use UsesMailcoachModels;

    public function execute(Campaign $campaign, array $attributes, Template $template = null): Campaign
    {
        $segmentClass = $attributes['segment_class'] ?? EverySubscriberSegment::class;

        $campaign->fill([
            'name' => $attributes['name'],
            'status' => ($attributes['type'] ?? null) === CampaignStatus::AUTOMATED
                ? CampaignStatus::AUTOMATED
                : ($campaign->status ?? CampaignStatus::DRAFT),
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $attributes['html'] ?? optional($template)->html,
            'structured_html' => $attributes['structured_html'] ?? optional($template)->structured_html,
            'track_opens' => $attributes['track_opens'] ?? true,
            'track_clicks' => $attributes['track_clicks'] ?? true,
            'last_modified_at' => now(),
        ]);

        if (! $campaign->isAutomated()) {
            $campaign->fill([
                'email_list_id' => $attributes['email_list_id'] ?? optional($this->getEmailListClass()::orderBy('name')->first())->id,
                'segment_class' => $segmentClass,
                'segment_description' => (new $segmentClass)->description(),
            ]);
        }

        $campaign->save();

        return $campaign->refresh();
    }
}
