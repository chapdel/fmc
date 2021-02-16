<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendAutomationMailAction extends AutomationAction
{
    use SerializesModels, UsesMailcoachModels;

    public AutomationMail $automationMail;

    public static function make(array $data): self
    {
        return new self(self::getAutomationMailClass()::find($data['automation_mail_id']));
    }

    public function __construct(AutomationMail $automationMail)
    {
        parent::__construct();

        $this->automationMail = $automationMail;
    }

    public static function getComponent(): ?string
    {
        return 'automation-mail-action';
    }

    public static function getName(): string
    {
        return __('Send an email');
    }

    public function getDescription(): string
    {
        return "{$this->automationMail->name}";
    }

    public function toArray(): array
    {
        return [
            'campaign_id' => $this->automationMail->id,
        ];
    }

    public function run(Subscriber $subscriber): void
    {
        $this->automationMail->send($subscriber);
    }
}
