<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->action = Action::factory()->create();
    test()->action->subscribers()->attach(SubscriberFactory::new()->create());

    test()->actionSubscriber = ActionSubscriber::first();

    TestTime::freeze();
});

it('never halts the automation', function () {
    $action = new WaitAction(CarbonInterval::days(1));

    expect($action->shouldHalt(test()->actionSubscriber))->toBeFalse();

    TestTime::addDay();

    expect($action->shouldHalt(test()->actionSubscriber))->toBeFalse();
});

it('will only continue when time has passed', function () {
    $action = new WaitAction(CarbonInterval::days(1));

    expect($action->shouldContinue(test()->actionSubscriber))->toBeFalse();

    TestTime::addDay();

    expect($action->shouldContinue(test()->actionSubscriber))->toBeTrue();
});

it('will return the correct query to only run on subscribers that need to continue', function () {
    $action = new WaitAction(CarbonInterval::days(1));
    $actionModel = Action::factory()->create([
        'action' => $action,
    ]);

    $subscriber1 = Subscriber::factory()->create();
    $subscriber2 = Subscriber::factory()->create();

    ActionSubscriber::create([
        'action_id' => $actionModel->id,
        'subscriber_id' => $subscriber1->id,
        'created_at' => now(),
    ]);

    ActionSubscriber::create([
        'action_id' => $actionModel->id,
        'subscriber_id' => $subscriber2->id,
        'created_at' => now()->subDays(2),
    ]);

    expect($action->getActionSubscribersQuery($actionModel)->count())->toBe(1);
});
