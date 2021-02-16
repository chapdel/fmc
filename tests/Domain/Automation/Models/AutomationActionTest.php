<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Models;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class AutomationActionTest extends TestCase
{
    use MatchesSnapshots;

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    protected Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->campaign = (new CampaignFactory())->create([
            'subject' => 'Welcome',
        ]);
    }

    /** @test * */
    public function it_can_store_itself()
    {
        $automation = Automation::create();

        $action = new HaltAction();

        $actionModel = $action->store(
            uuid: $uuid = Str::uuid(),
            automation: $automation,
        );

        $this->assertEquals(1, Action::count());
        $this->assertEquals($uuid, $actionModel->uuid);
        $this->assertEquals($automation->id, $actionModel->automation_id);
        $this->assertEquals(1, $actionModel->order);
        $this->assertInstanceOf(HaltAction::class, $actionModel->action);
    }

    /** @test * */
    public function it_can_reliably_get_the_next_action()
    {
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

        $this->assertEquals(4, Action::count());
        $this->assertTrue($child1->is($parentAction->nextAction($subscriber)));
        $this->assertTrue($child2->is($child1->action->nextAction($subscriber)));
        $this->assertTrue($parentModel2->is($child2->action->nextAction($subscriber)));
    }
}
