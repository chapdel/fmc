---
title: Creating custom triggers
weight: 3
---

If you're creating automations, you might run into a situation where you would like to have custom triggers for your automation.

Mailcoach allows you to extend the available triggers easily.

## Creating a custom trigger

There are two types of triggers: **event based triggers** and **scheduled triggers**, depending on your use case you can implement one of these.

Triggers must extend the `Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger` class.

This class has a `runAutomation` method that accepts one or more `Subscriber` objects. This method will kickstart the automation for the subscriber(s).

By default, the dropdown in the interface will show the classname of the trigger, you can implement the static method `getName()` to return a more user-friendly name for the trigger.

### Creating an event based trigger

Event based triggers start the automation, as the name suggests, when an event is triggered within your application. And must implement the `Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredByEvents` interface.

When creating an event based trigger, you'll need to implement the `subscribe` method of the `AutomationTrigger` class, this class is an [Event Subscriber](https://laravel.com/docs/master/events#event-subscribers), and gets registered automatically when attached to an automation.

We can look at the `SubscribedTrigger` as an example:

```php
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;

class SubscribedTrigger extends AutomationTrigger implements TriggeredByEvents
{
    public static function getName(): string
    {
        return (string) __('When a user subscribes');
    }

    public function subscribe($events): void
    {
        $events->listen(
            SubscribedEvent::class,
            function ($event) {
                $this->runAutomation($event->subscriber);
            }
        );
    }
}
```

As we can see here, the trigger will listen to the `Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent` event and start the automation with the subscriber from that event.

## Creating a scheduled trigger

Scheduled triggers are triggers that are ran by Laravel's scheduler component. An example of a scheduled based trigger is the `DateTrigger` that Mailcoach ships with.

You must implement the `Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredBySchedule` interface when creating a scheduled trigger.

These triggers implement the `trigger` method, where you can run any code you need to determine if the trigger should fire for a certain amount of subscribers.

The date trigger checks if the current date & time is the same as the date & time that was set in the trigger (more on creating setting fields below), and fires the automation for all its subscribers once the date is equal.

```php
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

public function trigger(Automation $automation): void
{
    if (! now()->startOfMinute()->equalTo($this->date->startOfMinute())) {
        return;
    }

    $this->runAutomation($automation->newSubscribersQuery());
}
```

### Creating setting fields & validation

Some triggers, like the `DateTrigger` require some user configuration in the UI. When you need this there's a few extra methods you can implement:

```php
class DateTrigger extends AutomationTrigger implements TriggeredBySchedule
{
    public CarbonInterface $date;
    
    public function __construct(CarbonInterface $date)
    {
        parent::__construct();
    
        $this->date = $date;
    }
        
    public static function getComponent(): ?string
    {
        return 'date-trigger';
    }
    
    public static function make(array $data): self
    {
        return new self((new DateTimeFieldRule())->parseDateTime($data['date']));
    }
    
    public static function rules(): array
    {
        return [
            'date' => ['required', new DateTimeFieldRule()],
        ];
    }
}
```

#### getComponent

The `getComponent()` method expects a [Livewire component's](https://laravel-livewire.com/docs/2.x/making-components) name to be returned. In this component, you can add any fields necessary for your trigger. 

This component should extend our `\Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent` class, which allows you to have access to the current automation inside your component.

For example, the `date-trigger` component renders a simple blade view with a date & time field:

```php
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        public function render()
    {
        return view('mailcoach::app.automations.components.triggers.dateTrigger');
    }
    }
}
```

And the view:

```html
<div>
    <x-mailcoach::date-time-field
        :label="__('Date')"
        name="date"
        :value="$automation->trigger->date ?? null"
        required
    />
</div>
```

#### make

The static `make()` method receives the validated data from the request, in this method you add the necessary parsing from raw data to your component's required data structure and call the constructor.

#### rules

The `rules` method on the Trigger class (not the Livewire component) allows you to specify rules for the fields you've created in the Livewire component.

## Registering your custom trigger

You can register your custom trigger by adding the classname to the `mailcoach.automation.flows.triggers` config key.
