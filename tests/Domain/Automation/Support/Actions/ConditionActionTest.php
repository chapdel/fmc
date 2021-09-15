<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\TestTime\TestTime;

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
    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    TestTime::addDay();

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    TestTime::addSecond();

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeTrue();
});

it('continues as soon as the subscriber has the tag', function () {
    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    test()->subscriber->addTag('some-tag');

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeTrue();
});

it('doesnt halt', function () {
    expect(test()->actionModel->action->shouldHalt(test()->subscriber))->toBeFalse();
});

it('determines the correct next action', function () {
    TestTime::addDays(2);

    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action)->toBeInstanceOf(HaltAction::class);

    test()->subscriber->addTag('some-tag');

    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action)->toBeInstanceOf(SendAutomationMailAction::class);
});
