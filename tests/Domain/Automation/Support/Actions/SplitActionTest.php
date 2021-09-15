<?php

use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

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
    test()->assertFalse(test()->actionModel->action->shouldHalt(test()->subscriber));
});

it('determines the correct next actions', function () {
    test()->assertInstanceOf(SendAutomationMailAction::class, test()->actionModel->action->nextActions(test()->subscriber)[0]->action);
    test()->assertEquals(test()->automationMail1->id, test()->actionModel->action->nextActions(test()->subscriber)[0]->action->automationMail->id);

    test()->assertInstanceOf(SendAutomationMailAction::class, test()->actionModel->action->nextActions(test()->subscriber)[1]->action);
    test()->assertEquals(test()->automationMail2->id, test()->actionModel->action->nextActions(test()->subscriber)[1]->action->automationMail->id);
});
