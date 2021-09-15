<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;


uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

    test()->emailList = EmailList::factory()->create();
});

it('triggers when a call is made to an endpoint', function () {
    Queue::fake();

    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new WebhookTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->emailList->subscribe('john@doe.com');

    Artisan::call(RunAutomationTriggersCommand::class);

    expect($automation->actions->first()->subscribers)->toBeEmpty();

    $subscriber = Subscriber::first();

    test()->loginToApi();

    test()->post(action(TriggerAutomationController::class, [$automation]), [
        'subscribers' => [$subscriber->id],
    ])->assertSuccessful();

    Queue::assertPushed(
        RunAutomationForSubscriberJob::class,
        function (RunAutomationForSubscriberJob $job) use ($subscriber, $automation) {
            expect($job->subscriber->email)->toBe($subscriber->email);
            expect($job->automation->id)->toBe($automation->id);

            return true;
        }
    );
});
