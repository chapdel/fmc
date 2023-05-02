<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SubscribeToEmailListAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;

beforeEach(function () {
    $this->subscriber = SubscriberFactory::new()->confirmed()->create();
    $this->emailList = EmailList::factory()->create();
    $this->action = new SubscribeToEmailListAction($this->emailList);
    $this->actionSubscriber = ActionSubscriber::create([
        'subscriber_id' => $this->subscriber->id,
        'action_id' => Action::factory()->create()->id,
    ]);
});

it('continues after execution', function () {
    expect($this->action->shouldContinue($this->actionSubscriber))->toBeTrue();
});

it('wont halt after execution', function () {
    expect($this->action->shouldHalt($this->actionSubscriber))->toBeFalse();
});

it('adds the subscriber to the new email list', function () {
    expect($this->emailList->subscribers()->count())->toBe(0);

    $this->action->run($this->actionSubscriber);

    expect($this->emailList->subscribers()->count())->toBe(1);
});

it('can skip confirmation', function () {
    $this->emailList->update(['requires_confirmation' => true]);

    expect($this->emailList->subscribers()->count())->toBe(0);

    $this->action->run($this->actionSubscriber);

    expect($this->emailList->subscribers()->count())->toBe(0);
    expect($this->emailList->allSubscribers()->count())->toBe(1);

    $this->action->skipConfirmation = true;

    $this->action->run($this->actionSubscriber);

    expect($this->emailList->subscribers()->count())->toBe(1);
    expect($this->emailList->allSubscribers()->count())->toBe(1);
});

it('copies attributes', function () {
    $this->subscriber->update([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'extra_attributes' => [
            'foo' => 'bar',
        ],
    ]);

    expect($this->emailList->subscribers()->count())->toBe(0);

    $this->action->run($this->actionSubscriber);

    $newSubscriber = $this->emailList->subscribers()->first();
    expect($newSubscriber->first_name)->toBe('John');
    expect($newSubscriber->last_name)->toBe('Doe');
    expect($newSubscriber->extra_attributes->foo)->toBe('bar');
});

it('can forward tags', function () {
    $this->subscriber->addTags(['foo', 'bar']);

    $this->action->forwardTags = true;

    $this->action->run($this->actionSubscriber);

    $newSubscriber = $this->emailList->subscribers()->first();

    expect($newSubscriber->hasTag('foo'))->toBeTrue();
    expect($newSubscriber->hasTag('bar'))->toBeTrue();
});

it('can forward and add new tags', function () {
    $this->subscriber->addTags(['foo', 'bar']);

    $this->action->forwardTags = true;
    $this->action->newTags = ['baz', 'bat'];

    $this->action->run($this->actionSubscriber);

    $newSubscriber = $this->emailList->subscribers()->first();

    expect($newSubscriber->hasTag('foo'))->toBeTrue();
    expect($newSubscriber->hasTag('bar'))->toBeTrue();
    expect($newSubscriber->hasTag('baz'))->toBeTrue();
    expect($newSubscriber->hasTag('bat'))->toBeTrue();
});
