<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Commands;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\CalculateAutomationMailStatisticsCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CalculateAutomationMailStatisticsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
    }

    /** @test */
    public function it_will_recalculate_statistics_of_active_automation_mails()
    {
        Bus::fake();

        $automationMail = AutomationMail::factory()->create();
        AutomationMail::factory()->create();

        Automation::create()
            ->runEvery(CarbonInterval::minute())
            ->to(EmailList::factory()->create())
            ->trigger(new SubscribedTrigger)
            ->chain([
                new SendAutomationMailAction($automationMail),
            ])->start();

        $this->artisan(CalculateAutomationMailStatisticsCommand::class)
            ->expectsOutput("Calculating statistics for automation mail id {$automationMail->id}...")
            ->assertExitCode(0);

        Bus::assertDispatched(CalculateStatisticsJob::class, 1);
    }

    /** @test */
    public function it_can_recalculate_the_statistics_of_a_single_automation_mail()
    {
        $automationMail = AutomationMail::factory()->create();

        $this->artisan(CalculateAutomationMailStatisticsCommand::class, ['automationMailId' => $automationMail->id])
            ->assertExitCode(0);

        $this->assertNotNull($automationMail->refresh()->statistics_calculated_at);
    }
}
