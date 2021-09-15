<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    TestTime::setTestNow(Carbon::create(2021, 01, 01));

    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail = AutomationMail::factory()->create();

    $automation = Automation::create()
        ->chain([
            new ConditionAction(
                CarbonInterval::day(),
                [
                    new SendAutomationMailAction(test()->automationMail),
                ],
                [
                    new HaltAction(),
                ],
                HasTagCondition::class,
                ['tag' => 'some-tag']
            ),
        ]);

    // Attach a dummy action so we have a pivot table
    test()->actionModel = $automation->actions->first();
    test()->actionModel->subscribers()->attach(test()->subscriber);
    test()->subscriber = test()->actionModel->subscribers->first();
});

it('doesnt continue while checking and the subscriber doesnt have the tag', function () {
    test()->assertFalse(test()->actionModel->action->shouldContinue(test()->subscriber));

    TestTime::addDay();

    test()->assertFalse(test()->actionModel->action->shouldContinue(test()->subscriber));

    TestTime::addSecond();

    test()->assertTrue(test()->actionModel->action->shouldContinue(test()->subscriber));
});

it('continues as soon as the subscriber has the tag', function () {
    test()->assertFalse(test()->actionModel->action->shouldContinue(test()->subscriber));

    test()->subscriber->addTag('some-tag');

    test()->assertTrue(test()->actionModel->action->shouldContinue(test()->subscriber));
});

it('doesnt halt', function () {
    test()->assertFalse(test()->actionModel->action->shouldHalt(test()->subscriber));
});

it('determines the correct next action', function () {
    TestTime::addDays(2);

    test()->assertInstanceOf(HaltAction::class, test()->actionModel->action->nextActions(test()->subscriber)[0]->action);

    test()->subscriber->addTag('some-tag');

    test()->assertInstanceOf(SendAutomationMailAction::class, test()->actionModel->action->nextActions(test()->subscriber)[0]->action);
});
