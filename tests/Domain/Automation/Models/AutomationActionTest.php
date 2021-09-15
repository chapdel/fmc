<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;


uses(MatchesSnapshots::class);

beforeEach(function () {
    Queue::fake();
});

it('can store itself', function () {
    $automation = Automation::create();

    $action = new HaltAction();

    $actionModel = $action->store(
        uuid: $uuid = Str::uuid(),
        automation: $automation,
    );

    expect(Action::count())->toEqual(1);
    expect($actionModel->uuid)->toEqual($uuid);
    expect($actionModel->automation_id)->toEqual($automation->id);
    expect($actionModel->order)->toEqual(1);
    expect($actionModel->action)->toBeInstanceOf(HaltAction::class);
});

it('can reliably get the next action', function () {
    $subscriber = Subscriber::factory()->create();

    $automation = Automation::create();
    $parentModel = (new HaltAction())->store(Str::uuid()->toString(), $automation);
    $parentAction = $parentModel->action;

    $child1 = Action::create([
        'parent_id' => $parentModel->id,
        'automation_id' => $automation->id,
        'uuid' => Str::uuid()->toString(),
        'action' => new HaltAction(),
        'order' => 0,
    ]);

    $child2 = Action::create([
        'parent_id' => $parentModel->id,
        'automation_id' => $automation->id,
        'uuid' => Str::uuid()->toString(),
        'action' => new HaltAction(),
        'order' => 1,
    ]);

    $parentAction2 = new HaltAction();
    $parentModel2 = $parentAction2->store(Str::uuid()->toString(), $automation);

    expect(Action::count())->toEqual(4);
    expect($child1->is($parentAction->nextActions($subscriber)[0]))->toBeTrue();
    expect($child2->is($child1->action->nextActions($subscriber)[0]))->toBeTrue();
    expect($parentModel2->is($child2->action->nextActions($subscriber)[0]))->toBeTrue();
});
