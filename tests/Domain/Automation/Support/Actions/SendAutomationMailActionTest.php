<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail = AutomationMail::factory()->create();
    test()->action = new SendAutomationMailAction(test()->automationMail);
});

it('continues after execution', function () {
    test()->assertTrue(test()->action->shouldContinue(test()->subscriber));
});

it('wont halt after execution', function () {
    test()->assertFalse(test()->action->shouldHalt(test()->subscriber));
});

it('sends an automation mail to the subscriber', function () {
    Queue::fake();

    test()->action->run(test()->subscriber);

    Queue::assertPushed(SendAutomationMailToSubscriberJob::class, function (SendAutomationMailToSubscriberJob $sendCampaignJob) {
        test()->assertTrue(test()->subscriber->is($sendCampaignJob->subscriber));
        test()->assertTrue(test()->automationMail->is($sendCampaignJob->automationMail));

        return true;
    });
});

it('wont send an automation mail twice', function () {
    test()->action->run(test()->subscriber);

    test()->action->run(test()->subscriber);

    test()->assertEquals(1, test()->automationMail->sends->count());
});
