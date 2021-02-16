<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class DateTriggerTest extends TestCase
{
    protected AutomationMail $automationMail;

    protected EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

        $this->emailList = EmailList::factory()->create();
    }

    /** @test * */
    public function it_triggers_on_a_specific_date()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new DateTrigger(Carbon::create(2020, 01, 02));

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->trigger($trigger)
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->emailList->subscribe('john@doe.com');

        Artisan::call(RunAutomationTriggersCommand::class);

        $this->assertEmpty($automation->actions->first()->subscribers);

        TestTime::addDay();

        Artisan::call(RunAutomationTriggersCommand::class);

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }
}
