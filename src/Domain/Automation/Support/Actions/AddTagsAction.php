<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class AddTagsAction extends AutomationAction
{
    public array $tags;

    public static function make(array $data): self
    {
        return new self(explode(',', $data['tags']));
    }

    public function __construct(array $tags)
    {
        parent::__construct();

        $this->tags = $tags;
    }

    public static function getName(): string
    {
        return __('Add tags');
    }

    public static function getComponent(): ?string
    {
        return 'add-tags-action';
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
