<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Data;

readonly class SubscriberClickedCampaignLinkQueryConditionData
{
    protected function __construct(
        public int $campaignId,
        public ?string $link = null,
    ) {
    }

    public static function make(int $campaignId, string $link = null): self
    {
        return new self($campaignId, $link);
    }

    public static function fromArray(array $data): self
    {
        return new self($data['campaignId'], $data['link'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'campaignId' => $this->campaignId,
            'link' => $this->link,
        ];
    }
}
