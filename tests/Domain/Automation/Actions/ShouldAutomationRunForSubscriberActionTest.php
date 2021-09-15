<?php


use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\ShouldAutomationRunForSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentQueryOnlyJohn;



beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
    test()->automationMail = AutomationMail::factory()->create();
    test()->action = resolve(ShouldAutomationRunForSubscriberAction::class);

    Queue::fake();
});

it('returns false when the subscriber is already in the automation', function () {
    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])->start();

    test()->refreshServiceProvider();

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    expect(test()->action->execute($automation, $jane))->toBeTrue();

    $automation->actions->first()->subscribers()->attach($jane);

    expect(test()->action->execute($automation, $jane))->toBeFalse();
});

it('returns false if the subscriber isnt subscribed', function () {
    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])->start();

    test()->refreshServiceProvider();

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    expect(test()->action->execute($automation, $jane))->toBeTrue();

    $jane->unsubscribe();

    expect(test()->action->execute($automation, $jane))->toBeFalse();
});

it('returns false when the subscriber isnt in the segment', function () {
    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->segment(TestSegmentQueryOnlyJohn::class)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])->start();

    test()->refreshServiceProvider();

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');
    $john = test()->emailList->subscribeSkippingConfirmation('john@example.com');

    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    expect(test()->action->execute($automation, $jane))->toBeFalse();
    expect(test()->action->execute($automation, $john))->toBeTrue();
});
