<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagRemovedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class TagRemovedTriggerTest extends TestCase
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
    public function it_triggers_when_a_tag_is_removed_from_a_subscriber()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new TagRemovedTrigger('opened');

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

        $this->emailList->subscribe('john@doe.com');

        Subscriber::first()->addTag('opened');

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        Subscriber::first()->removeTag('opened');

        Queue::assertPushed(
            RunAutomationForSubscriberJob::class,
            function (RunAutomationForSubscriberJob $job) use ($automation) {
                $this->assertSame('john@doe.com', $job->subscriber->email);

                return true;
            }
        );
    }
}
