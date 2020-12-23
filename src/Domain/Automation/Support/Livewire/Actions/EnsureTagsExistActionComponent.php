<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class EnsureTagsExistActionComponent extends AutomationActionComponent
{
    public string $checkFor = '';

    public array $tags = [];

    public array $defaultActions = [];

    protected $listeners = ['automationBuilderUpdated', 'tagChainUpdated', 'validationFailed'];

    public function getData(): array
    {
        return [
            'checkFor' => $this->checkFor,
            'tags' => $this->tags,
            'defaultActions' => $this->defaultActions,
        ];
    }

    public function tagChainUpdated(array $data): void
    {
        $this->tags[$data['index']] = [
            'tag' => $data['tag'],
            'actions' => $data['actions'],
        ];

        $this->emitUp('actionUpdated', $this->getData());
    }

    public function automationBuilderUpdated(array $data): void
    {
        if ($data['name'] !== 'default-actions') {
            return;
        }

        $this->defaultActions = $data['actions'];

        $this->emitUp('actionUpdated', $this->getData());
    }

    public function rules(): array
    {
        return [
            'checkFor' => ['required'],
            'tags' => ['nullable', 'array'],
            'defaultActions' => ['nullable', 'array'],
        ];
    }

    public function addTag(): void
    {
        $this->tags[] = [
            'tag' => '',
            'actions' => [],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.ensureTagsExistAction');
    }
}
