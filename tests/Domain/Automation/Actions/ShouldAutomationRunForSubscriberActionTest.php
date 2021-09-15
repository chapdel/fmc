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

uses(TestCase::class);

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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    test()->assertTrue(test()->action->execute($automation, $jane));

    $automation->actions->first()->subscribers()->attach($jane);

    test()->assertFalse(test()->action->execute($automation, $jane));
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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    test()->assertTrue(test()->action->execute($automation, $jane));

    $jane->unsubscribe();

    test()->assertFalse(test()->action->execute($automation, $jane));
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

    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    test()->assertFalse(test()->action->execute($automation, $jane));
    test()->assertTrue(test()->action->execute($automation, $john));
});
