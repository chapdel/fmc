<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SubscribedTriggerTest extends TestCase
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
    public function it_triggers_when_a_subscriber_is_subscribed()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new SubscribedTrigger();

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn($trigger)
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $this->emailList->subscribeSkippingConfirmation('john@doe.com');

        Queue::assertPushed(
            RunAutomationForSubscriberJob::class,
            function (RunAutomationForSubscriberJob $job) use ($automation) {
                $this->assertSame('john@doe.com', $job->subscriber->email);
                $this->assertSame($automation->id, $job->automation->id);

                return true;
            }
        );
    }
}
