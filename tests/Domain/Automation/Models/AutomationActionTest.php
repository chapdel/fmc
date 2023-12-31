<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;

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

it('handles old non serialized actions', function () {
    $oldAction = new Action();
    $oldAction->setRawAttributes([
        'order' => 0,
        'action' => 'O:61:"Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction":2:{s:8:"interval";O:21:"Carbon\CarbonInterval":28:{s:9:" * tzName";N;s:7:" * step";N;s:22:" * localMonthsOverflow";N;s:21:" * localYearsOverflow";N;s:25:" * localStrictModeEnabled";N;s:24:" * localHumanDiffOptions";N;s:22:" * localToStringFormat";N;s:18:" * localSerializer";N;s:14:" * localMacros";N;s:21:" * localGenericMacros";N;s:22:" * localFormatFunction";N;s:18:" * localTranslator";N;s:1:"y";i:0;s:1:"m";i:0;s:1:"d";i:0;s:1:"h";i:0;s:1:"i";i:10;s:1:"s";i:0;s:1:"f";d:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:4:"uuid";s:36:"7c9a20bb-148a-4e67-a7a6-037bbf40fadb";}',
    ]);
    $oldAction->save();

    expect($oldAction->action::class)->toEqual(WaitAction::class);
    expect($oldAction->getRawOriginal('action'))->toEqual('O:61:"Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction":2:{s:8:"interval";O:21:"Carbon\CarbonInterval":28:{s:9:" * tzName";N;s:7:" * step";N;s:22:" * localMonthsOverflow";N;s:21:" * localYearsOverflow";N;s:25:" * localStrictModeEnabled";N;s:24:" * localHumanDiffOptions";N;s:22:" * localToStringFormat";N;s:18:" * localSerializer";N;s:14:" * localMacros";N;s:21:" * localGenericMacros";N;s:22:" * localFormatFunction";N;s:18:" * localTranslator";N;s:1:"y";i:0;s:1:"m";i:0;s:1:"d";i:0;s:1:"h";i:0;s:1:"i";i:10;s:1:"s";i:0;s:1:"f";d:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:4:"uuid";s:36:"7c9a20bb-148a-4e67-a7a6-037bbf40fadb";}');

    $newAction = new WaitAction(CarbonInterval::day());
    $oldAction->action = $newAction;
    $oldAction->save();

    expect($oldAction->action::class)->toEqual(WaitAction::class);
    expect($oldAction->getRawOriginal('action'))->toEqual(base64_encode(serialize($newAction)));
});
