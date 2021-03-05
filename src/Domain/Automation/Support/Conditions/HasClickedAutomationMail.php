<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasClickedAutomationMail implements Condition
{
    use UsesMailcoachModels;

    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __('Has clicked automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || ! $data['automation_mail_id']) {
            return '';
        }

        $mail = AutomationMail::find($data['automation_mail_id']);

        return (string) __(':mail - :url', [
            'mail' => $mail->name,
            'url' => isset($data['automation_mail_link_url']) && $data['automation_mail_link_url']
                ? $data['automation_mail_link_url']
                : __('Any link'),
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
                'nullable',
                'string',
            ],
        ];
    }

    public function check(): bool
    {
        $query = AutomationMailClick::query()
            ->where('subscriber_id', $this->subscriber->id)
            ->whereHas('send', function (Builder $query) {
                $query->where('automation_mail_id', $this->data['automation_mail_id']);
            });

        if ($this->data['automation_mail_link_url'] ?? false) {
            $query->whereHas('link', function (Builder $query) {
                $query->where('url', $this->data['automation_mail_link_url']);
            });
        }

        return $query->exists();
    }
}
