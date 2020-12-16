<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class RemoveTagsAction extends AutomationAction
{
    public array $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    public static function getName(): string
    {
        return __('Remove tags');
    }

    public function getDescription(): string
    {
        return implode(', ', $this->tags);
    }

    public static function getComponent(): ?string
    {
        return 'remove-tags-action';
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
        $subscriber->removeTags($this->tags);
    }
}
