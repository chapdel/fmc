<?php

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::setTestNow(Carbon::create(2021, 01, 01));

    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->automationMail = AutomationMail::factory()->create();

    test()->automation = Automation::create()
        ->chain([
            new ConditionAction(
                checkFor: CarbonInterval::day(),
                yesActions: [
                    new SendAutomationMailAction(test()->automationMail),
                ],
                noActions: [
                    new HaltAction(),
                ],
                condition: HasTagCondition::class,
                conditionData: ['tag' => 'some-tag']
            ),
        ]);

    // Attach a dummy action so we have a pivot table
    test()->actionModel = test()->automation->actions->first();
    test()->actionModel->subscribers()->attach(test()->subscriber);
    test()->subscriber = test()->actionModel->subscribers->first();
});

it('doesnt continue while checking and the subscriber doesnt have the tag', function () {
    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    TestTime::addDay();

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    TestTime::addSecond();

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeTrue();
});

it('continues as soon as the subscriber has the tag', function () {
    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeFalse();

    test()->subscriber->addTag('some-tag');

    expect(test()->actionModel->action->shouldContinue(test()->subscriber))->toBeTrue();
});

it('doesnt halt', function () {
    expect(test()->actionModel->action->shouldHalt(test()->subscriber))->toBeFalse();
});

it('determines the correct next action', function () {
    TestTime::addDays(2);

    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action)->toBeInstanceOf(HaltAction::class);

    test()->subscriber->addTag('some-tag');

    expect(test()->actionModel->action->nextActions(test()->subscriber)[0]->action)->toBeInstanceOf(SendAutomationMailAction::class);
});

it('can store actions and preserves uuids', function () {
    $originalActionUuid = test()->actionModel->action->uuid;
    $originalYesUuid = test()->actionModel->action->toArray()['yesActions'][0]['uuid'];
    $originalNoUuid = test()->actionModel->action->toArray()['noActions'][0]['uuid'];

    dump([$originalActionUuid, $originalYesUuid, $originalNoUuid]);

    expect(Action::count())->toBe(3);
    expect(Action::where('uuid', $originalActionUuid)->count())->toBe(1);
    expect(Action::where('uuid', $originalYesUuid)->count())->toBe(1);
    expect(Action::where('uuid', $originalNoUuid)->count())->toBe(1);

    $newActions = test()->automation->actions()
        ->withCount(['completedSubscribers', 'activeSubscribers', 'haltedSubscribers'])
        ->get()
        ->map(function (Action $action) {
            try {
                return $action->toLivewireArray();
            } catch (ModelNotFoundException) {
                $action->delete();

                return null;
            }
        })
        ->filter()
        ->values()
        ->toArray();

    test()->automation->chain($newActions);

    expect(Action::where('uuid', $originalActionUuid)->count())->toBe(1);
    expect(Action::where('uuid', $originalYesUuid)->count())->toBe(1);
    expect(Action::where('uuid', $originalNoUuid)->count())->toBe(1);

    test()->actionModel = test()->automation->actions()->first();

    expect(Action::count())->toBe(3);

    expect(test()->actionModel->action->uuid)->toBe($originalActionUuid);
    expect(test()->actionModel->action->toArray()['yesActions'][0]['uuid'])->toBe($originalYesUuid);
    expect(test()->actionModel->action->toArray()['noActions'][0]['uuid'])->toBe($originalNoUuid);
});
