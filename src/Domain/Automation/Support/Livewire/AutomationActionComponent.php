<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

use Illuminate\Support\MessageBag;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationActionComponent extends AutomationComponent
{
    use UsesMailcoachModels;

    public int $index = 0;

    protected $listeners = ['validationFailed'];

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
}
