<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Spatie\Mailcoach\Database\Factories\AutomationMailFactory;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailTestJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class AutomationMail extends Sendable
{
    public $table = 'mailcoach_automation_mails';

    protected $casts = [
        'add_subscriber_tags' => 'boolean',
        'add_subscriber_link_tags' => 'boolean',
    ];

    public static function booted(): void
    {
        static::created(function (AutomationMail $automationMail) {
            if (! $automationMail->contentItem) {
                $contentItem = $automationMail->contentItem()->firstOrCreate();
                $automationMail->setRelation('contentItem', $contentItem);
            }
        });

        static::deleting(function (AutomationMail $automationMail) {
            $automationMail->contentItem->delete();
        });
    }

    public function isReady(): bool
    {
        return $this->contentItem->isReady();
    }

    public function send(ActionSubscriber $actionSubscriber): self
    {
        $this->ensureSendable();

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        dispatch(new SendAutomationMailToSubscriberJob($this, $actionSubscriber));

        return $this;
    }

    public function sendTestMail(string|array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            (new SendAutomationMailTestJob($this, $email))->handle();
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.automations.webview', $this->uuid));
    }

    public function dispatchCalculateStatistics(): void
    {
        dispatch(new CalculateStatisticsJob($this));
    }

    protected static function newFactory(): AutomationMailFactory
    {
        return new AutomationMailFactory();
    }
}
