<?php

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->action = new RemoveTagsAction(['some-tag', 'another-tag']);
    test()->actionSubscriber = ActionSubscriber::create([
        'subscriber_id' => test()->subscriber->id,
        'action_id' => Action::factory()->create()->id,
    ]);
});

it('continues after execution', function () {
    expect(test()->action->shouldContinue(test()->actionSubscriber->fresh()))->toBeTrue();
});

it('wont halt after execution', function () {
    expect(test()->action->shouldHalt(test()->actionSubscriber->fresh()))->toBeFalse();
});

it('removes a tag from the subscriber', function () {
    test()->subscriber->addTag('some-tag');
    test()->subscriber->addTag('another-tag');

    expect(test()->subscriber->fresh()->hasTag('some-tag'))->toBeTrue();
    expect(test()->subscriber->fresh()->hasTag('another-tag'))->toBeTrue();

    test()->action->run(test()->actionSubscriber->fresh());

    expect(test()->subscriber->fresh()->hasTag('some-tag'))->toBeFalse();
    expect(test()->subscriber->fresh()->hasTag('another-tag'))->toBeFalse();
});
