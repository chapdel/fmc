<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendAutomationMailAction extends AutomationAction
{
    use SerializesModels;
    use UsesMailcoachModels;

    public AutomationMail $automationMail;

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::React;
    }

    public static function make(array $data): self
    {
        return new self(self::getAutomationMailClass()::findOrFail($data['automation_mail_id']));
    }

    public function __construct(AutomationMail $automationMail)
    {
        parent::__construct();

        $this->automationMail = $automationMail;
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::automation-mail-action';
    }

    public static function getName(): string
    {
        return (string) __mc('Send an email');
    }

    public function toArray(): array
    {
        return [
            'automation_mail_id' => $this->automationMail->id,
        ];
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        $this->automationMail->send($actionSubscriber);
    }

    public function getActionSubscribersQuery(Action $action): Builder|\Illuminate\Database\Eloquent\Builder|Relation
    {
        $hasNextActions = count($this->nextActionsForAction($action));

        if (! $hasNextActions) {
            return $action->pendingActionSubscribers()->whereNull('run_at');
        }

        return $action->pendingActionSubscribers();
    }
}
