<?php

namespace Spatie\Mailcoach\Tests\Support\Automation\Triggers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Automation\Actions\CampaignAction;
use Spatie\Mailcoach\Support\Automation\Triggers\DateTrigger;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class DateTriggerTest extends TestCase
{
    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())->create([
            'subject' => 'Welcome',
        ]);
    }

    /** @test * */
    public function it_triggers_on_a_specific_date()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new DateTrigger(Carbon::create(2020, 01, 02));

        $automation = Automation::create()
            ->name('New year!')
            ->to($this->campaign->emailList)
            ->trigger($trigger)
            ->chain([
                new CampaignAction($this->campaign),
            ])
            ->start();

        $this->campaign->emailList->subscribe('john@doe.com');

        Artisan::call(RunAutomationTriggersCommand::class);

        $this->assertEmpty($automation->actions->first()->subscribers);

        TestTime::addDay();

        Artisan::call(RunAutomationTriggersCommand::class);

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }
}
