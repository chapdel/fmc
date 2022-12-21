<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class GetReplaceContextForSendableAction
{
    public function execute(?Sendable $sendable): array
    {
        if (! $sendable) {
            return [];
        }

        return match (true) {
            $sendable instanceof Campaign => [
                'campaign' => $sendable->toArray(),
                'websiteCampaignUrl' => $sendable->emailList->has_website
                    ? $sendable->websiteUrl()
                    : '',
                'webviewUrl' => $sendable->webviewUrl(),
            ],
            $sendable instanceof AutomationMail => [
                'automation_mail' => $sendable->toArray(),
                'webviewUrl' => $sendable->webviewUrl(),
            ],
            default => [],
        };
    }
}
