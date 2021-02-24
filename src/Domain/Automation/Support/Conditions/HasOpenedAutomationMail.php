<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasOpenedAutomationMail implements Condition
{
    use UsesMailcoachModels;

    public function __construct(
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return __('Has opened automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || ! $data['automation_mail_id']) {
            return '';
        }

        return __(':mail', ['mail' => AutomationMail::find($data['automation_mail_id'])->name]);
    }

    public static function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailTableName(), 'id'),
            ],
        ];
    }

    public function check(): bool
    {
        return AutomationMailOpen::query()
            ->where('subscriber_id', $this->subscriber->id)
            ->where('automation_mail_id', $this->data['automation_mail_id'])
            ->exists();
    }
}
