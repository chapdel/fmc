<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\CampaignAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagAddedTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SubscribedTriggerTest extends TestCase
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
    public function it_triggers_when_a_subscriber_is_subscribed()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new SubscribedTrigger();

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->campaign->emailList)
            ->trigger($trigger)
            ->chain([
                new CampaignAction($this->campaign),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $this->campaign->emailList->subscribeSkippingConfirmation('john@doe.com');

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }
}
