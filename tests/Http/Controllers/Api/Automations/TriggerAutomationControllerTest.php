<?php

use Carbon\CarbonInterval;
use Illuminate\Queue\Connectors\SyncConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->automationMail = AutomationMail::factory()->create([
        'subject' => 'Welcome',
    ]);

    test()->emailList = EmailList::factory()->create();
});

it('responds with 200', function () {
    test()->withExceptionHandling();

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new WebhookTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    $subscriber = test()->emailList->subscribe('john@doe.com');

    test()->postJson(action(TriggerAutomationController::class, [$automation]), [
        'subscribers' => [$subscriber->uuid],
    ])->assertStatus(200);
});

it('needs an automation with a webhook trigger', function () {
    test()->withExceptionHandling();

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new DateTrigger(now()))
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    $subscriber = test()->emailList->subscribe('john@doe.com');

    test()->postJson(action(TriggerAutomationController::class, [$automation]), [
        'subscribers' => [$subscriber->uuid],
    ])->assertStatus(400)
        ->assertSee('This automation does not have a Webhook trigger.');
});

it('only handles subscribers from the email list', function () {
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new WebhookTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    $subscriber1 = test()->emailList->subscribe('john@doe.com');
    $subscriber2 = SubscriberFactory::new()->create();

    test()->postJson(action(TriggerAutomationController::class, [$automation]), [
        'subscribers' => [$subscriber1->uuid, $subscriber2->uuid],
    ])->assertSuccessful();

    expect($automation->actions()->first()->subscribers->count())->toEqual(1);
});

it('needs a subscribed subscriber', function () {
    $manager = new QueueManager($this->app);
    $manager->addConnector('sync', function () {
        return new SyncConnector();
    });
    Queue::swap($manager);

    test()->withExceptionHandling();

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new WebhookTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    $subscriber1 = test()->emailList->subscribe('john1@doe.com');
    $subscriber2 = test()->emailList->subscribe('john2@doe.com');
    $subscriber2->unsubscribe();

    test()->postJson(action(TriggerAutomationController::class, [$automation]), [
        'subscribers' => [$subscriber1->uuid, $subscriber2->uuid],
    ])->assertSuccessful();

    expect($automation->actions()->first()->subscribers->count())->toEqual(1);
});
