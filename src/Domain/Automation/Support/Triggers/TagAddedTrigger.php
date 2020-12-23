<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Events\TagAddedEvent;

class TagAddedTrigger extends AutomationTrigger
{
    public string $tag = '';

    public function __construct(string $tag, ?string $uuid = null)
    {
        parent::__construct($uuid);

        $this->tag = $tag;
    }

    public static function getName(): string
    {
        return __('When a tag gets added to a subscriber');
    }

    public static function getComponent(): ?string
    {
        return 'tag-added-trigger';
    }

    public static function rules(): array
    {
        return [
            'tag' => ['required'],
        ];
    }

    public function handleSubscribed(TagAddedEvent $event)
    {
        if ($event->tag->name === $this->tag) {
            $this->fire($event->subscriber);
        }
    }

    public function subscribe($events): void
    {
        $events->listen(
            TagAddedEvent::class,
            function ($event) {
                $this->handleSubscribed($event);
            }
        );
    }

    public static function make(array $data): self
    {
        return new self($data['tag']);
    }
}
