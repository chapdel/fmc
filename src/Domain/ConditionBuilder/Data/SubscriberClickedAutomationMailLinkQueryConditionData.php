<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Data;

readonly class SubscriberClickedAutomationMailLinkQueryConditionData
{
    protected function __construct(
        public int $automationMailId,
        public ?string $link = null,
    ) {
    }

    public static function make(int $automationMailId, string $link = null): self
    {
        return new self($automationMailId, $link);
    }

    public static function fromArray(array $data): self
    {
        return new self($data['automationMailId'], $data['link'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'automationMailId' => $this->automationMailId,
            'link' => $this->link,
        ];
    }
}
