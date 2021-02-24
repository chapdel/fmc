<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasClickedAutomationMail implements Condition
{
    use UsesMailcoachModels;

    public function __construct(
        private Subscriber $subscriber,
        private array $data,
    ) {}

    public static function getName(): string
    {
        return __('Has clicked automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || !$data['automation_mail_id']) {
            return '';
        }

        if (! isset($data['automation_mail_link_url']) || !$data['automation_mail_link_url']) {
            return '';
        }

        $mail = AutomationMail::find($data['automation_mail_id']);

        return __(':mail - :url', [
            'mail' => $mail->name,
            'url' => $data['automation_mail_link_url'],
        ]);
    }

    public static function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailTableName(), 'id'),
            ],
            'automation_mail_link_url' => [
                'required',
            ],
        ];
    }

    public function check(): bool
    {
        return $this->subscriber
            ->clicks()
            ->where('url', $this->data['automation_mail_link_url'])
            ->exists();
    }
}
