<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\TagRemovedEvent;

class TagRemovedTrigger extends AutomationTrigger
{
    public string $tag = '';

    public function __construct(string $tag, ?string $uuid = null)
    {
        parent::__construct($uuid);

        $this->tag = $tag;
    }

    public static function getName(): string
    {
        return __('When a tag gets removed from a subscriber');
    }

    public static function getComponent(): ?string
    {
        return 'tag-removed-trigger';
    }

    public static function rules(): array
    {
        return [
            'tag' => ['required'],
        ];
    }

    public function handleTagRemoved(TagRemovedEvent $event)
    {
        if ($event->tag->name === $this->tag) {
            $this->fire($event->subscriber);
        }
    }

    public function subscribe($events): void
    {
        $events->listen(
            TagRemovedEvent::class,
            function ($event) {
                $this->handleTagRemoved($event);
            }
        );
    }

    public static function make(array $data): self
    {
        return new self($data['tag']);
    }
}
