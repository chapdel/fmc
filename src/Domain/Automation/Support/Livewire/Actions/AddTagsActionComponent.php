<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Spatie\ValidationRules\Rules\Delimited;

class AddTagsActionComponent extends AutomationActionComponent
{
    public string $tags = '';

    public function getData(): array
    {
        return [
            'tags' => $this->tags,
        ];
    }

    public function rules(): array
    {
        return [
            'tags' => ['required', new Delimited('string')],
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <x-mailcoach::text-field
                    :label="__('Tags to add')"
                    :required="true"
                    name="tags"
                    wire:model="tags"
                />
            </div>
        blade;
    }
}
