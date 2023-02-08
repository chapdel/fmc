<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendWebhookAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Http::preventStrayRequests();

    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail = AutomationMail::factory()->create();
    test()->action = new SendWebhookAction('https://example.com', 'abc12345');
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

it('sends a webhook', function () {
    Queue::fake();

    test()->action->run(test()->actionSubscriber);

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $job) {
        expect($job->webhookUrl)->toBe('https://example.com');
        expect($job->headers['Signature'])->toBe(hash_hmac('sha256', json_encode($job->payload), 'abc12345'));

        return true;
    });
});

it('only runs when needed and not when there is no next action', function () {
    $action = new SendWebhookAction('https://example.com', 'abc12345');
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
        'run_at' => now()->subDay(),
    ]);

    expect($action->getActionSubscribersQuery($actionModel)->count())->toBe(1);

    $action = new HaltAction();
    Action::factory()->create([
        'automation_id' => $actionModel->automation_id,
        'action' => $action,
        'order' => 2,
    ]);

    expect($action->getActionSubscribersQuery($actionModel)->count())->toBe(2);
});
