<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagAddedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class TagAddedTriggerTest extends TestCase
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
    public function it_triggers_when_a_subscriber_gets_a_tag()
    {
        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new TagAddedTrigger('opened');

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

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        Subscriber::first()->addTag('clicked');

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        Subscriber::first()->addTag('opened');

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }

    /** @test * */
    public function it_triggers_when_a_new_subscriber_has_a_tag()
    {
        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $trigger = new TagAddedTrigger('opened');

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

        $pendingSubscriber = Subscriber::createWithEmail('john@doe.com')
            ->tags(['opened'])
            ->subscribeTo($this->emailList);

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }

    /** @test * */
    public function it_triggers_when_a_new_confirmed_subscriber_has_a_tag()
    {
        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $this->emailList->update([
            'requires_confirmation' => true,
        ]);

        $trigger = new TagAddedTrigger('opened');

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

        $subscriber = Subscriber::createWithEmail('john@doe.com')
            ->tags(['opened'])
            ->subscribeTo($this->emailList);

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $subscriber->confirm();

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }
}
