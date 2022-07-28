<?php

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AddTagsAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;

beforeEach(function () {
    $subscriber = SubscriberFactory::new()->confirmed()->create();
    $action = Action::factory()->create();
    test()->actionSubscriber = ActionSubscriber::create([
        'subscriber_id' => $subscriber->id,
        'action_id' => $action->id,
    ]);
    test()->action = new AddTagsAction(['some-tag', 'another-tag']);
});

it('continues after execution', function () {
    expect(test()->action->shouldContinue(test()->actionSubscriber))->toBeTrue();
});

it('wont halt after execution', function () {
    expect(test()->action->shouldHalt(test()->actionSubscriber))->toBeFalse();
});

it('adds tags to the subscriber', function () {
    expect(test()->actionSubscriber->subscriber->hasTag('some-tag'))->toBeFalse();
    expect(test()->actionSubscriber->subscriber->hasTag('another-tag'))->toBeFalse();

    test()->action->run(test()->actionSubscriber);

    expect(test()->actionSubscriber->subscriber->hasTag('some-tag'))->toBeTrue();
    expect(test()->actionSubscriber->subscriber->hasTag('another-tag'))->toBeTrue();
});
