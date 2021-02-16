<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateAutomationMailAction
{
    use UsesMailcoachModels;

    public function execute(AutomationMail $automationMail, array $attributes): AutomationMail
    {
        $automationMail->fill([
            'name' => $attributes['name'],
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? '',
            'track_opens' => $attributes['track_opens'] ?? true,
            'track_clicks' => $attributes['track_clicks'] ?? true,
            'utm_tags' => $attributes['utm_tags'] ?? true,
            'last_modified_at' => now(),
        ]);

        $automationMail->save();

        return $automationMail->refresh();
    }
}
