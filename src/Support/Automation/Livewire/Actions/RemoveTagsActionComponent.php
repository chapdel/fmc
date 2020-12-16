<?php

namespace Spatie\Mailcoach\Support\Automation\Livewire\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Support\Automation\Livewire\AutomationComponent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class RemoveTagsActionComponent extends AutomationComponent
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
                    :label="__('Tags to remove')"
                    :required="true"
                    name="tags"
                    wire:model="tags"
                />
            </div>
        blade;
    }
}
