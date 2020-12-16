<?php

namespace Spatie\Mailcoach\Tests\Models;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Enums\AutomationStatus;
use Spatie\Mailcoach\Jobs\SendCampaignToSubscriberJob;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Automation\Actions\CampaignAction;
use Spatie\Mailcoach\Support\Automation\Actions\HaltAction;
use Spatie\Mailcoach\Support\Automation\Actions\WaitAction;
use Spatie\Mailcoach\Support\Automation\Triggers\SubscribedAutomationTrigger;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TestTime\TestTime;

class AutomationTest extends TestCase
{
    use MatchesSnapshots;

    /** @var \Spatie\Mailcoach\Models\Campaign */
    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->campaign = (new CampaignFactory())->create([
            'subject' => 'Welcome',
        ]);
    }

    /** @test */
    public function the_default_status_is_draft()
    {
        $automation = Automation::create();

        $this->assertEquals(AutomationStatus::PAUSED, $automation->status);
    }

    /** @test */
    public function it_can_run_a_welcome_automation()
    {
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($this->campaign->emailList)
            ->trigger(new SubscribedAutomationTrigger)
            ->chain([
                new CampaignAction($this->campaign),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, $automation->actions()->count());
        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $this->campaign->emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Queue::assertPushed(SendCampaignToSubscriberJob::class, function (SendCampaignToSubscriberJob $job) {
            $this->assertTrue($job->campaign->is($this->campaign));
            $this->assertEquals('john@doe.com', $job->subscriber->email);

            return true;
        });
    }

    /** @test */
    public function a_halted_action_will_stop_the_automation()
    {
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($this->campaign->emailList)
            ->trigger(new SubscribedAutomationTrigger)
            ->chain([
                new HaltAction,
                new CampaignAction($this->campaign),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(2, $automation->actions->count());
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());

        $this->campaign->emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Queue::assertNothingPushed();
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(0, $automation->actions->last()->subscribers()->count());
    }

    /** @test */
    public function it_continues_once_the_action_returns_true()
    {
        TestTime::freeze();

        $automation = Automation::create()
            ->name('Welcome email')
            ->to($this->campaign->emailList)
            ->trigger(new SubscribedAutomationTrigger)
            ->chain([
                new WaitAction(CarbonInterval::days(1)),
                new CampaignAction($this->campaign),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(2, $automation->actions->count());
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());

        $this->campaign->emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Queue::assertNothingPushed();
        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(0, $automation->actions->last()->subscribers()->count());

        TestTime::addDay();

        Artisan::call(RunAutomationActionsCommand::class);

        Queue::assertPushed(SendCampaignToSubscriberJob::class);
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(1, $automation->actions->last()->subscribers()->count());
    }

    /** @test * */
    public function it_can_sync_actions_successfully()
    {
        $this->markTestSkipped('TODO: Saving has changed');
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($this->campaign->emailList)
            ->trigger(new SubscribedAutomationTrigger)
            ->chain([]);

        $this->assertEquals([], $automation->actions->toArray());

        $automation->chain([
            new WaitAction(CarbonInterval::days(1)),
        ]);

        $this->assertEquals([
            serialize(new WaitAction(CarbonInterval::days(1))),
        ], $automation->actions()->pluck('action')->map(fn ($action) => serialize($action))->toArray());
        $firstId = $automation->actions()->first()->id;

        $automation->chain([
            new WaitAction(CarbonInterval::days(1)),
            new CampaignAction($this->campaign),
        ]);

        $this->assertEquals([
            serialize(new WaitAction(CarbonInterval::days(1))),
            serialize(new CampaignAction($this->campaign)),
        ], $automation->actions()->pluck('action')->map(fn ($action) => serialize($action))->toArray());
        $this->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action

        $automation->chain([
            new WaitAction(CarbonInterval::days(1)),
            new CampaignAction($this->campaign),
            new WaitAction(CarbonInterval::days(2)),
        ]);

        $this->assertEquals([
            serialize(new WaitAction(CarbonInterval::days(1))),
            serialize(new CampaignAction($this->campaign)),
            serialize(new WaitAction(CarbonInterval::days(2))),
        ], $automation->actions()->pluck('action')->map(fn ($action) => serialize($action))->toArray());
        $this->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action
    }
}
