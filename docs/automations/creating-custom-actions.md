---
title: Creating custom actions
weight: 2
---

If you're creating automations, you might run into a situation where you would like to have custom actions for your automation. Mailcoach allows you to extend the available actions easily.

## Creating a custom action

Actions must extend the `Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction` class.

By default, the dropdown in the interface will show the classname of the action, you can implement the static method `getName()` to return a more user-friendly name for the action.

The `getCategory` method is required to implement and should be a value of the `Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum` enum.

There are three optional methods you can implement that control the flow of the automation once a subscriber reaches your action:

`run(Subscriber $subscriber): void`

This method is executed once, when the subscriber is added to this action for the first time, in the `SendAutomationMailAction` we use this to send the automation mail to the Subscriber:

```php
public function run(Subscriber $subscriber): void
{
    $this->automationMail->send($subscriber);
}
```

`shouldContinue(Subscriber $subscriber): bool`

This method returns `true` by default, which means the Subscriber is moved on to the next action once the action's `run` method has been called.

In the `WaitAction`, this is used to wait a certain duration before the subscriber is moved to the next action:

```php
public function shouldContinue(Subscriber $subscriber): bool
{
    if ($subscriber->pivot->created_at <= now()->sub($this->interval)) {
        return true;
    }

    return false;
}
```
Inside actions, you have access to the `pivot` of the `subscriber_actions` relationship, which allows you to access when a subscriber was added to the action. The duration in the `WaitAction` is set in the UI, more on that below.

`shouldHalt(Subscriber $subscriber): bool`

This method returns `false` by default, this method allows you to completely halt the automation flow for a subscriber when returning `true`, even if there would be other actions after the current one.

### Creating settings fields & validation

Most actions, like the `WaitAction` require some user configuration in the UI. When you need this there's a few extra methods you can implement:

```php

class WaitAction extends AutomationAction
{
    public CarbonInterval $interval;

    public function __construct(CarbonInterval $interval)
    {
        parent::__construct();

        $this->interval = $interval;
    }

    public static function getComponent(): ?string
    {
        return 'wait-action';
    }

    public static function make(array $data): self
    {
        return new self(CarbonInterval::createFromDateString("{$data['length']} {$data['unit']}"));
    }

    public function toArray(): array
    {
        [$length, $unit] = explode(' ', $this->interval->forHumans());

        return [
            'length' => $length,
            'unit' => Str::plural($unit),
        ];
    }
}
```

#### getComponent

The `getComponent()` method expects a [Livewire component's](https://laravel-livewire.com/docs/2.x/making-components) name to be returned. In this component, you can add any fields necessary for your trigger. 

This component should extend our `\Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent` class, which allows you to have access to the current automation inside your component.

The `getData` method has to return the data you want stored inside the action.

For example, the `wait-action` component renders a simple blade view with a text field and has some validation rules:

The validation rules are stored on the Livewire component here, as the automation builder, which is also a Livewire component, handles all validation.

```php
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class WaitActionComponent extends AutomationActionComponent
{
    public string $length = '1';

    public string $unit = 'days';

    public array $units = [
        'minutes' => 'Minute',
        'hours' => 'Hour',
        'days' => 'Day',
        'weeks' => 'Week',
        'months' => 'Month',
    ];

    public function getData(): array
    {
        return [
            'length' => (int) $this->length,
            'unit' => $this->unit,
        ];
    }

    public function rules(): array
    {
        return [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in([
                'minutes',
                'hours',
                'days',
                'weeks',
                'months',
            ])],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.waitAction');
    }
}
```

When creating an action component's view, you should wrap this inside the `<x-mailcoach::automation-action>` blade component. 

This will make sure the edit & save buttons are shown correctly. This is the view of the `wait-action` component as an example:

```html
<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__('Wait for ') }}
        <span class="legend-accent">
            {{ ($length && $unit && $interval = \Carbon\CarbonInterval::createFromDateString("{$length} {$unit}")) ? $interval->cascade()->forHumans() : '…' }}
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-8 sm:col-span-4">
            <x-mailcoach::text-field
                :label="__('Length')"
                :required="true"
                name="length"
                wire:model="length"
                type="number"
            />
        </div>

        <div class="col-span-4 sm:col-span-2">
        <x-mailcoach::select-field
            :label="__('Unit')"
            :required="true"
            name="unit"
            wire:model="unit"
            :options="
                collect($units)
                    ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                    ->toArray()
            "
        />
        </div>
    </x-slot>
</x-mailcoach::automation-action>
```

#### make

The static `make()` method receives the validated data from the request, in this method you add the necessary parsing from raw data to your action's required data structure and call the constructor.

#### toArray

The `toArray()` method is used to return the data in a format fit for processing in the Livewire component.

## Registering your custom action

You can register your custom action by adding the classname to the `mailcoach.automation.flows.actions` config key.
