<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\TestTime\TestTime;

it('throttles dispatching automation mail sends', function () {
    config()->set('mail.mailers.log.mails_per_timespan', 2);
    config()->set('mail.mailers.log.timespan_in_seconds', 3);

    Mail::fake();
    TestTime::unfreeze();

    $automationMail = AutomationMail::factory()->create();

    Send::factory(3)->create(['content_item_id' => ContentItem::factory()->automationMail()->create([
        'model_id' => $automationMail->id,
    ])]);

    $action = resolve(SendAutomationMailsAction::class);
    $action->execute();

    Mail::assertSent(MailcoachMail::class, 3);

    $jobDispatchTimes = Send::get()
        ->map(function (Send $send) {
            return $send->sending_job_dispatched_at;
        })
        ->toArray();

    [$sendTime1, $sendTime2, $sendTime3] = $jobDispatchTimes;

    expect($sendTime1->diffInSeconds($sendTime2))->toEqual(0);
    expect($sendTime2->diffInSeconds($sendTime3))->toBeGreaterThanOrEqual(3);
});

it('will throttle processing mail jobs', function () {
    config()->set('mail.mailers.log.mails_per_timespan', 2);
    config()->set('mail.mailers.log.timespan_in_seconds', 3);

    // Fake the throttle not working
    $this->partialMock(SimpleThrottle::class)
        ->shouldReceive('forMailer')->andReturnSelf()
        ->shouldReceive('hit')
        ->andReturnSelf();

    Mail::fake();
    TestTime::unfreeze();

    $automationMail = AutomationMail::factory()->create();

    Send::factory(3)->create(['content_item_id' => ContentItem::factory()->automationMail()->create([
        'model_id' => $automationMail->id,
    ])]);

    $action = resolve(SendAutomationMailsAction::class);
    $action->execute();

    Mail::assertSent(MailcoachMail::class, 2); // The third one is released
    sleep(3);
    $action->execute();

    $sentTimes = Send::get()
        ->map(function (Send $send) {
            return $send->sent_at;
        })
        ->toArray();

    [$sendTime1, $sendTime2, $sendTime3] = $sentTimes;

    expect($sendTime1->diffInSeconds($sendTime2))->toEqual(0);
    expect($sendTime2->diffInSeconds($sendTime3))->toBeGreaterThanOrEqual(3);
});

it('will retry stuck pending sends', function () {
    Queue::fake();

    $action = app(SendAutomationMailsAction::class);

    $automationMail = AutomationMail::factory()->create();
    $contentItem = ContentItem::factory()->automationMail()->create([
        'model_id' => $automationMail->id,
    ]);

    Send::factory()->create([
        'sending_job_dispatched_at' => now()->subMinutes(35),
        'content_item_id' => $contentItem->id,
    ]);

    Send::factory()->create([
        'content_item_id' => $contentItem->id,
        'sending_job_dispatched_at' => now()->subMinutes(25),
    ]);

    $action->execute();

    Queue::assertPushed(SendAutomationMailJob::class, 1);
});
