<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import automations', function () {
    $emailList = EmailList::factory()->create();
    $automation = Automation::create()
        ->name('Welcome email')
        ->runEvery(CarbonInterval::minute())
        ->to($emailList)
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new WaitAction(CarbonInterval::days(1)),
            new HaltAction(),
        ])
        ->start();

    (new ExportAutomationsJob($this->disk->path('import'), [$automation->id]))->handle();

    expect($this->disk->exists('import/automations.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_triggers.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_actions.csv'))->toBeTrue();

    Automation::query()->delete();
    Trigger::query()->delete();
    Action::query()->delete();

    expect(Automation::count())->toBe(0);
    expect(Trigger::count())->toBe(0);
    expect(Action::count())->toBe(0);

    (new ImportAutomationsJob())->handle();
    (new ImportAutomationsJob())->handle(); // Don't import duplicates

    expect(Automation::count())->toBe(1);
    expect(Trigger::count())->toBe(1);
    expect(Action::count())->toBe(2);
    expect(Automation::first()->status)->toBe(AutomationStatus::PAUSED);
    expect(Automation::first()->actions()->count())->toBe(2);
    expect(Automation::first()->triggers()->count())->toBe(1);
    expect(Automation::first()->getTrigger())->toBeInstanceOf(SubscribedTrigger::class);
});
