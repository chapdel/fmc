<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail = AutomationMail::factory()->create();
    test()->action = new SendAutomationMailAction(test()->automationMail);
    test()->actionSubscriber = ActionSubscriber::create([
        'subscriber_id' => test()->subscriber->id,
        'action_id' => Action::factory()->create()->id,
    ]);
});

it('continues after execution', function () {
    expect(test()->action->shouldContinue(test()->actionSubscriber))->toBeTrue();
});

it('wont halt after execution', function () {
    expect(test()->action->shouldHalt(test()->actionSubscriber))->toBeFalse();
});

it('sends an automation mail to the subscriber', function () {
    Queue::fake();

    test()->action->run(test()->actionSubscriber);

    Queue::assertPushed(SendAutomationMailToSubscriberJob::class, function (SendAutomationMailToSubscriberJob $sendCampaignJob) {
        expect(test()->actionSubscriber->is($sendCampaignJob->actionSubscriber))->toBeTrue();
        expect(test()->automationMail->is($sendCampaignJob->automationMail))->toBeTrue();

        return true;
    });
});

it('wont send an automation mail twice', function () {
    test()->action->run(test()->actionSubscriber);

    test()->action->run(test()->actionSubscriber);

    expect(test()->automationMail->sends->count())->toEqual(1);
});
