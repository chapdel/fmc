<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Data;

readonly class SubscriberClickedCampaignLinkQueryConditionData
{
    protected function __construct(
        public string $campaignId,
        public ?string $link = null,
    ) {
    }

    public static function make(string $campaignId, string $link = null): self
    {
        return new self($campaignId, $link);
    }

    public function toArray(): array
    {
        return [
            'campaign_id' => $this->campaignId,
            'link' => $this->link,
        ];
    }
}
