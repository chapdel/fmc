<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\AutomationActions;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class AddTagsAction extends AutomationAction
{
    public array $tags;

    public function __construct(array $tags)
    {
        parent::__construct();

        $this->tags = $tags;
    }

    public static function getName(): string
    {
        return __('Add tags');
    }

    public function getDescription(): string
    {
        return implode(', ', $this->tags);
    }

    public static function getComponent(): ?string
    {
        return 'add-tags-action';
    }

    public static function make(array $data): self
    {
        return new self(explode(',', $data['tags']));
    }

    public function toArray(): array
    {
        return [
            'tags' => implode(',', $this->tags),
        ];
    }

    public function run(Subscriber $subscriber): void
    {
        $subscriber->addTags($this->tags);
    }
}
