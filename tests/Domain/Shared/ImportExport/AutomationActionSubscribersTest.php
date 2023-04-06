<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationActionSubscribersJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import automation action subscribers', function () {
    $emailList = EmailList::factory()->create();

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('Welcome email')
        ->runEvery(CarbonInterval::minute())
        ->to($emailList)
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new WaitAction(CarbonInterval::days(1)),
        ])
        ->start();
    $subscriber = Subscriber::factory()->create(['email_list_id' => $emailList->id]);

    $automation->run($subscriber);

    expect(ActionSubscriber::count())->toBe(1);

    (new ExportAutomationsJob('import', [$automation->id]))->handle();

    expect($this->disk->exists('import/automation_action_subscribers-1.csv'))->toBeTrue();

    ActionSubscriber::query()->delete();

    expect(ActionSubscriber::count())->toBe(0);

    (new ImportAutomationActionSubscribersJob())->handle();
    (new ImportAutomationActionSubscribersJob())->handle(); // Don't import duplicates

    expect(ActionSubscriber::count())->toBe(1);
});
