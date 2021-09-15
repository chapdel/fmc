<?php

use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::setTestNow(Carbon::create(2021, 01, 01));

    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail1 = AutomationMail::factory()->create();
    test()->automationMail2 = AutomationMail::factory()->create();

    $automation = Automation::create()
        ->chain([
            new SplitAction(
                [
                    new SendAutomationMailAction(test()->automationMail1),
                ],
                [
                    new SendAutomationMailAction(test()->automationMail2),
                ],
            ),
        ]);

    // Attach a dummy action so we have a pivot table
    test()->actionModel = $automation->actions->first();
    test()->actionModel->subscribers()->attach(test()->subscriber);
    test()->subscriber = test()->actionModel->subscribers->first();
});

it('doesnt halt', function () {
    expect(test()->actionModel->action->shouldHalt(test()->subscriber))->toBeFalse();
});

it('determines the correct next actions', function () {
    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action)->toBeInstanceOf(SendAutomationMailAction::class);
    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action->automationMail->id)->toEqual(test()->automationMail1->id);

    expect(test()->actionModel->action->nextActions(test()->subscriber)[1]->action)->toBeInstanceOf(SendAutomationMailAction::class);
    expect(test()->actionModel->action->nextActions(test()->subscriber)[1]->action->automationMail->id)->toEqual(test()->automationMail2->id);
});
