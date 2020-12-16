<?php

namespace Spatie\Mailcoach\Support\Automation\Livewire;

use Illuminate\Support\MessageBag;
use Livewire\Component;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

abstract class AutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public int $index = 0;

    public string $actionClass;

    public array $actionData = [];

    protected $listeners = ['validationFailed'];

    public function mount()
    {
        foreach ($this->actionData as $key => $value) {
            $this->$key = $value;
        }
    }

    public function validationFailed(array $errors)
    {
        $this->setErrorBag(new MessageBag($errors));
    }

    public function rules(): array
    {
        return [];
    }

    public function updated($fieldName): void
    {
        $this->resetValidation($fieldName);

        $this->emitUp('actionUpdated', $this->getData());
    }

    abstract public function getData(): array;
    abstract public function render();
}
