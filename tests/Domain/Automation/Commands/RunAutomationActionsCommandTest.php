<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\TestTime\TestTime;

it('runs automations that are started', function () {
    $automation = Automation::create()
        ->to(EmailList::factory()->create())
        ->triggerOn(new SubscribedTrigger())
        ->runEvery(CarbonInterval::minute())
        ->chain([
            new HaltAction(),
        ]);

    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->fresh()->run_at)->toBeNull();

    $automation->start();

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertNotNull($automation->fresh()->run_at);
});

it('respects the interval', function () {
    TestTime::setTestNow(Carbon::create(2021, 01, 01, 10));

    $automation = Automation::create()
        ->to(EmailList::factory()->create())
        ->triggerOn(new SubscribedTrigger())
        ->runEvery(CarbonInterval::minutes(10))
        ->chain([
            new HaltAction(),
        ])
        ->start();

    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->fresh()->run_at)->toEqual(Carbon::create(2021, 01, 01, 10));

    TestTime::addMinutes(5);
    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->fresh()->run_at)->toEqual(Carbon::create(2021, 01, 01, 10));

    TestTime::addMinutes(5);
    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->fresh()->run_at)->toEqual(Carbon::create(2021, 01, 01, 10, 10));
});
