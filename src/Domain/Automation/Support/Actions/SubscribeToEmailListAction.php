<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SubscribeToEmailListAction extends AutomationAction
{
    use SerializesModels;
    use UsesMailcoachModels;

    public EmailList $emailList;

    public bool $skipConfirmation;

    public bool $forwardTags;

    public array $newTags;

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::React;
    }

    public static function make(array $data): self
    {
        return new self(
            emailList: self::getEmailListClass()::findOrFail($data['email_list_id']),
            skipConfirmation: $data['skip_confirmation'] ?? false,
            forwardTags: $data['forward_tags'] ?? false,
            newTags: array_map('trim', explode(',', $data['new_tags'] ?? '')),
        );
    }

    public function __construct(
        EmailList $emailList,
        bool $skipConfirmation = false,
        bool $forwardTags = false,
        array $newTags = []
    ) {
        parent::__construct();

        $this->emailList = $emailList;
        $this->skipConfirmation = $skipConfirmation;
        $this->forwardTags = $forwardTags;
        $this->newTags = $newTags;
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::email-list-action';
    }

    public static function getName(): string
    {
        return (string) __mc('Add to email list');
    }

    public function toArray(): array
    {
        return [
            'email_list_id' => $this->emailList->id,
            'skip_confirmation' => $this->skipConfirmation,
            'forward_tags' => $this->forwardTags,
            'new_tags' => implode(',', array_map('trim', $this->newTags)),
        ];
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber $pendingSubscriber */
        $pendingSubscriber = self::getSubscriberClass()::createWithEmail($actionSubscriber->subscriber->email);

        if ($this->skipConfirmation) {
            $pendingSubscriber->skipConfirmation();
        }

        $tags = $this->newTags;

        if ($this->forwardTags) {
            $tags = array_merge($tags, $actionSubscriber->subscriber->tags->pluck('name')->toArray());
        }

        $pendingSubscriber->tags($tags);

        $pendingSubscriber->attributes = array_filter([
            'first_name' => $actionSubscriber->subscriber->first_name,
            'last_name' => $actionSubscriber->subscriber->last_name,
            'extra_attributes' => $actionSubscriber->subscriber->extra_attributes->toArray(),
        ]);

        $pendingSubscriber->subscribeTo($this->emailList);
    }
}
