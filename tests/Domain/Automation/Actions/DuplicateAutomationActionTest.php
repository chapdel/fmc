<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\DuplicateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Mailcoach;

beforeEach(function () {
    $this->action = Mailcoach::getAutomationActionClass('duplicate_automation', DuplicateAutomationAction::class);
});

it('can duplicate an automation', function () {
    $automation = \Spatie\Mailcoach\Domain\Automation\Models\Automation::factory()->create();

    $duplicate = $this->action->execute($automation);

    expect($automation->id)->not()->toBe($duplicate->id);
});

it('can duplicate an automation with actions', function () {
    $emailList = EmailList::factory()->create();

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('Welcome email')
        ->to($emailList)
        ->runEvery(CarbonInterval::minute())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new WaitAction(CarbonInterval::day()),
        ])
        ->start();

    $duplicate = $this->action->execute($automation);

    expect($duplicate->email_list_id)->toBe($emailList->id);
    expect($duplicate->status)->toBe(AutomationStatus::Paused);
    expect($duplicate->actions()->count())->toBe(1);
});

it('can duplicate an automation with nested actions', function () {
    $emailList = EmailList::factory()->create();

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('Welcome email')
        ->to($emailList)
        ->runEvery(CarbonInterval::minute())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new SplitAction(
                leftActions: [
                    new WaitAction(CarbonInterval::day()),
                ],
                rightActions: [
                    new WaitAction(CarbonInterval::day()),
                ],
            ),
        ])
        ->start();

    $duplicate = $this->action->execute($automation);

    expect($duplicate->allActions()->count())->toBe(3);
});

it('can duplicate an automation with deeply nested actions', function () {
    $emailList = EmailList::factory()->create();

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('Welcome email')
        ->to($emailList)
        ->runEvery(CarbonInterval::minute())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new ConditionAction(
                checkFor: CarbonInterval::day(),
                yesActions: [
                    new ConditionAction(
                        checkFor: CarbonInterval::day(),
                        yesActions: [
                            new WaitAction(CarbonInterval::day()),
                        ],
                        noActions: [
                            new WaitAction(CarbonInterval::day()),
                        ]
                    ),
                ],
                noActions: [
                    new WaitAction(CarbonInterval::day()),
                ],
            ),
        ])
        ->start();

    $duplicate = $this->action->execute($automation);

    expect($duplicate->allActions()->count())->toBe(3);
});
