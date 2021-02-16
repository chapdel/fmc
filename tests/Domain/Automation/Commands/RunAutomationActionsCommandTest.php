<?php

namespace Spatie\Mailcoach\Tests;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\TestTime\TestTime;

class RunAutomationActionsCommandTest extends TestCase
{
    /** @test * */
    public function it_runs_automations_that_are_started()
    {
        $automation = Automation::create()
            ->to(EmailList::factory()->create())
            ->trigger(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minute())
            ->chain([
                new HaltAction(),
            ]);

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertNull($automation->fresh()->run_at);

        $automation->start();

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertNotNull($automation->fresh()->run_at);
    }

    /** @test * */
    public function it_respects_the_interval()
    {
        TestTime::setTestNow(Carbon::create(2021, 01, 01, 10));

        $automation = Automation::create()
            ->to(EmailList::factory()->create())
            ->trigger(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minutes(10))
            ->chain([
                new HaltAction(),
            ])
            ->start();

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertEquals(Carbon::create(2021, 01, 01, 10), $automation->fresh()->run_at);

        TestTime::addMinutes(5);
        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertEquals(Carbon::create(2021, 01, 01, 10), $automation->fresh()->run_at);

        TestTime::addMinutes(5);
        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertEquals(Carbon::create(2021, 01, 01, 10, 10), $automation->fresh()->run_at);
    }
}
