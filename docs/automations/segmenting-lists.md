---
title: Segmenting lists
weight: 10
---

If you wish to send an automation to only a part of an email list you can use a segment when creating your automation. A segment is a class that is responsible for selecting subscribers on an email list. It should always extend `Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment`

## A first example

Here's a silly segment that will only select subscribers whose email address begin with an 'a'

```php
class OnlyEmailAddressesStartingWithA extends Segment
{
    public function shouldSend(Subscriber $subscriber): bool
    {
        return Str::startsWith($subscriber->email, 'a');
    }
}
```

When create an automation this is how the segment can be used:

```php
Automation::create()
   ->segment(OnlyEmailAddressesStartingWithA::class);
```

## Using an instantiated Segment object

Here's the same segment that will only select subscribers whose email address begin with a configurable character 'b'

```php
class OnlyEmailAddressesStartingWith extends Segment
{
    public string $character;

    public function __construct(string $character) {
        $this->character = $character;
    }

    public function shouldSend(Subscriber $subscriber): bool
    {
        return Str::startsWith($subscriber->email, $this->character);
    }
}
```

When sending a campaign this is how the segment can be used:

```php
Automation::create()
    ->segment(new OnlyEmailAddressesStartingWith('b'));
```

The object will be serialized when saved to the automation, and unserialized when used for segmenting.

## Using a query

If you have a very large list, it might be better to use a query to select the subscribers of your segment. This can be done with the `subscribersQuery` method on a segment.

Here's an example:

```php
class OnlyEmailAddressesStartingWithA extends Segment
{
    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $subscribersQuery->where('email','like', 'a%');
    }
}
```

No matter what you do in `subscribersQuery`, the package will never mail people that haven't subscribed to the email list you're sending the automation to.

## Segment description

`Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment` allows us to give our custom segment a unique name. This is required by the interface and can be done very easily:

```php
public function description(): string
{
    return 'My cool segment';
}
```

## Accessing the Automation model

If you need to get any `automation` details somewhere in your segment logic, you can use `$this->segmentable` to access the model object of the automation that is being sent.
