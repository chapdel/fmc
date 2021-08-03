<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Models;

use Carbon\CarbonInterval;
use Illuminate\Mail\MailManager;
use Illuminate\Queue\Connectors\SyncConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\NoTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\AddRandomTagAction;
use Spatie\Mailcoach\Tests\TestClasses\AllowDuplicateSendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\AllowDuplicateShouldAutomationRunForSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TestTime\TestTime;
use Swift_Message;

class AutomationTest extends TestCase
{
    use MatchesSnapshots;

    protected AutomationMail $automationMail;

    public function setUp(): void
    {
        parent::setUp();

        $this->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);
    }

    /** @test */
    public function the_default_status_is_paused()
    {
        $automation = Automation::create();

        $this->assertEquals(AutomationStatus::PAUSED, $automation->status);
    }

    /** @test */
    public function it_can_run_a_welcome_automation()
    {
        Mail::fake();

        $emailList = EmailList::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($emailList)
            ->runEvery(CarbonInterval::minute())
            ->triggerOn(new SubscribedTrigger)
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, $automation->actions()->count());
        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertTrue($mail->hasTo('john@doe.com'));

            return true;
        });
    }

    /** @test */
    public function a_halted_action_will_stop_the_automation()
    {
        Queue::fake();

        $emailList = EmailList::factory()->create();

        $automation = Automation::create()
            ->name('Welcome email')
            ->to($emailList)
            ->runEvery(CarbonInterval::minute())
            ->triggerOn(new SubscribedTrigger)
            ->chain([
                new HaltAction,
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(2, $automation->actions->count());
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());

        $emailList->subscribe('john@doe.com');

        $this->processQueuedJobs();

        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Queue::assertNotPushed(SendAutomationMailToSubscriberJob::class);
        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(0, $automation->actions->last()->subscribers()->count());
    }

    /** @test */
    public function it_continues_once_the_action_returns_true()
    {
        Mail::fake();
        TestTime::freeze();

        $emailList = EmailList::factory()->create();

        $automation = Automation::create()
            ->name('Welcome email')
            ->runEvery(CarbonInterval::minute())
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger)
            ->chain([
                new WaitAction(CarbonInterval::days(1)),
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(2, $automation->actions->count());
        $this->assertEquals(0, $automation->actions->first()->subscribers()->count());

        $emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Mail::assertNothingSent();
        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(0, $automation->actions->last()->subscribers()->count());

        TestTime::addDay();

        Artisan::call(RunAutomationActionsCommand::class);

        Mail::assertSent(MailcoachMail::class);
        $this->assertEquals(1, $automation->actions->first()->subscribers()->count());
        $this->assertEquals(1, $automation->actions->last()->subscribers()->count());
    }

    /** @test * */
    public function it_can_sync_actions_successfully()
    {
        Queue::fake();

        $emailList = EmailList::factory()->create();

        $automation = Automation::create()
            ->name('Welcome email')
            ->runEvery(CarbonInterval::minute())
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([]);

        $this->assertEquals([], $automation->actions->toArray());

        $waitAction = new WaitAction(CarbonInterval::day());
        $waitAction->uuid = Str::uuid()->toString();

        $automation->chain([
            $waitAction,
        ]);

        $this->assertEquals([
            serialize($waitAction),
        ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

        $firstId = $automation->actions()->first()->id;

        $campaignAction = new SendAutomationMailAction($this->automationMail);
        $campaignAction->uuid = Str::uuid()->toString();

        $automation->chain([
            $waitAction,
            $campaignAction,
        ]);

        $this->assertEquals([
            serialize($waitAction),
            serialize($campaignAction),
        ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

        $this->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action

        $wait2days = new WaitAction(CarbonInterval::days(2));
        $automation->chain([
            $waitAction,
            $campaignAction,
            $wait2days,
        ]);

        $this->assertEquals([
            serialize($waitAction),
            serialize($campaignAction),
            serialize($wait2days),
        ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

        $this->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action
    }

    /** @test * */
    public function it_can_create_and_run_a_complicated_automation()
    {
        Mail::fake();

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $automatedMail1 = AutomationMail::factory()->create();
        $automatedMail2 = AutomationMail::factory()->create();
        $automatedMail3 = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->name('Getting started with Mailcoach')
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minutes(10)) // Run through the automation and check actions every 10 min
            ->chain([
                new WaitAction(CarbonInterval::day()), // Wait one day
                new SendAutomationMailAction($automatedMail1), // Send first email
                new ConditionAction(
                    checkFor: CarbonInterval::days(3), // Keep checking tags for 3 days, if not they get the default or halted
                    yesActions: [
                        new WaitAction(CarbonInterval::day()), // Wait one day
                        new SendAutomationMailAction($automatedMail2), // Send first email
                    ],
                    noActions: [
                        new WaitAction(CarbonInterval::days(2)), // Wait 2 days
                    ],
                    condition: HasTagCondition::class,
                    conditionData: ['tag' => 'mc::campaign-1-clicked-1']
                ),
                new WaitAction(CarbonInterval::days(3)), // Wait 3 days
                new SendAutomationMailAction($automatedMail3),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, Automation::count());
        tap(Automation::first(), function (Automation $automation) {
            $this->assertEquals(CarbonInterval::minutes(10), $automation->interval);
            $this->assertInstanceOf(SubscribedTrigger::class, $automation->triggers->first()->getAutomationTrigger());
        });
        $this->assertEquals(8, Action::count());

        $this->assertEquals(2, Action::where('key', 'yesActions')->count());
        $this->assertEquals(1, Action::where('key', 'noActions')->count());

        $subscriber = $automation->emailList->subscribe('john@doe.com');

        // Wait for a day
        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        Artisan::call(RunAutomationActionsCommand::class);
        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        TestTime::addDay()->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        // CampaignAction continues straight to next action after running
        Mail::assertSent(MailcoachMail::class, 1);
        $this->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);

        // EnsureTagsExist checks for 3 days
        TestTime::addDay()->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);
        TestTime::addDays(2)->addSecond();
        $this->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);

        // Adding a tag causes it to go to the next
        $subscriber->addTag('mc::campaign-1-clicked-1');
        Artisan::call(RunAutomationActionsCommand::class);
        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        $this->assertEquals(CarbonInterval::day(), $subscriber->currentActions($automation)->first()->action->interval);

        // Wait for a day
        TestTime::addDays(2)->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        // Campaign Action sends again
        Mail::assertSent(MailcoachMail::class, 2);

        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        $this->assertEquals(CarbonInterval::days(3), $subscriber->currentActions($automation)->first()->action->interval);

        // Wait for 3 days
        TestTime::addDays(3)->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        // Execute last CampaignAction
        Artisan::call(RunAutomationActionsCommand::class);
        Mail::assertSent(MailcoachMail::class, 3);

        $this->assertInstanceOf(SendAutomationMailAction::class, $subscriber->currentActions($automation)->first()->action);
        $this->assertNotNull($subscriber->currentActions($automation)->first()->pivot->run_at);
    }

    /** @test * */
    public function it_handles_nested_conditions_correctly()
    {
        TestTime::freeze();

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $automatedMail1 = AutomationMail::factory()->create();
        $automatedMail2 = AutomationMail::factory()->create();
        $automatedMail3 = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->name('Testing nested conditions')
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minutes(10)) // Run through the automation and check actions every 10 min
            ->chain([
                new ConditionAction(
                    checkFor: CarbonInterval::day(),
                    yesActions: [
                        new ConditionAction(
                            checkFor: CarbonInterval::day(),
                            yesActions: [
                                new SendAutomationMailAction($automatedMail1),
                            ],
                            noActions: [
                                new SendAutomationMailAction($automatedMail2),
                            ],
                            condition: HasTagCondition::class,
                            conditionData: ['tag' => 'yes-tag-2'],
                        ),
                    ],
                    noActions: [
                        new SendAutomationMailAction($automatedMail3),
                    ],
                    condition: HasTagCondition::class,
                    conditionData: ['tag' => 'yes-tag-1']
                ),
                new WaitAction(CarbonInterval::days(3)),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEquals(6, Action::count());

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber1 */
        $subscriber1 = $automation->emailList->subscribe('subscriber1@example.com');
        $subscriber1->addTags(['yes-tag-1', 'yes-tag-2']); // Should receive mail 1

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber2 */
        $subscriber2 = $automation->emailList->subscribe('subscriber2@example.com');
        $subscriber2->addTags(['yes-tag-1']); // Should receive mail 2

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber3 */
        $subscriber3 = $automation->emailList->subscribe('subscriber3@example.com'); // Should receive mail 3

        Artisan::call(RunAutomationActionsCommand::class);

        // Subscriber 1 went straight through all the "yes" paths to the send
        $this->assertEquals(WaitAction::class, $subscriber1->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber1->currentActions($automation)->first()->action->toArray());

        // And has received automationmail 1
        $this->assertSame(1, $subscriber1->sends()->count());
        $this->assertSame($automatedMail1->id, $subscriber1->sends->first()->automationMail->id);

        $this->assertEquals('yes-tag-2', $subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);
        $this->assertEquals('yes-tag-1', $subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);

        TestTime::addDay();

        Artisan::call(RunAutomationActionsCommand::class);

        // Subscriber 2 went through the first yes, and second no
        $this->assertEquals(WaitAction::class, $subscriber2->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber2->currentActions($automation)->first()->action->toArray());

        // And received automationmail 2
        $this->assertSame(1, $subscriber2->sends()->count());
        $this->assertSame($automatedMail2->id, $subscriber2->sends->first()->automationMail->id);

        // Subscriber 3 went through the first no
        $this->assertEquals(WaitAction::class, $subscriber3->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber3->currentActions($automation)->first()->action->toArray());

        // And received automationmail 3
        $this->assertSame(1, $subscriber3->sends()->count());
        $this->assertSame($automatedMail3->id, $subscriber3->sends->first()->automationMail->id);

        // Only 3 mails were sent in total
        $this->assertEquals(3, Send::count());
    }

    /** @test */
    public function the_automation_mail_can_use_custom_mailable()
    {
        $manager = new QueueManager($this->app);
        $manager->addConnector('sync', function () {
            return new SyncConnector();
        });
        Queue::swap($manager);

        config()->set('mailcoach.automation.mailer', 'array');

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $automationMail = AutomationMail::factory()->create();

        $automationMail->useMailable(TestMailcoachMail::class);

        $automation = Automation::create()
            ->name('Getting started with Mailcoach')
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minutes(10))
            ->chain([
                new SendAutomationMailAction($automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, Action::count());

        $automation->emailList->subscribe('subscriber@example.com');

        TestTime::addDay();

        Artisan::call(RunAutomationActionsCommand::class);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }

    /** @test */
    public function it_can_run_a_split_automation()
    {
        Mail::fake();

        $emailList = EmailList::factory()->create();

        $automationMail2 = AutomationMail::factory()->create(['subject' => 'Welcome 2']);

        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($emailList)
            ->runEvery(CarbonInterval::minute())
            ->triggerOn(new SubscribedTrigger)
            ->chain([
                new SplitAction(
                    [new SendAutomationMailAction($this->automationMail)],
                    [new SendAutomationMailAction($automationMail2)],
                ),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, $automation->actions()->count());
        $this->assertEquals(3, $automation->allActions()->count());
        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $emailList->subscribe('john@doe.com');

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Mail::assertSent(MailcoachMail::class, 2);
    }

    /** @test * */
    public function it_will_stop_the_automation_if_the_user_is_unsubscribed()
    {
        Mail::fake();

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $automatedMail1 = AutomationMail::factory()->create();
        $automatedMail2 = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->name('Getting started with Mailcoach')
            ->to($emailList)
            ->triggerOn(new SubscribedTrigger())
            ->runEvery(CarbonInterval::minutes(10)) // Run through the automation and check actions every 10 min
            ->chain([
                new WaitAction(CarbonInterval::day()), // Wait one day
                new SendAutomationMailAction($automatedMail1), // Send first email
                new WaitAction(CarbonInterval::days(3)), // Wait 3 days
                new SendAutomationMailAction($automatedMail2),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, Automation::count());
        $this->assertEquals(4, Action::count());

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $automation->emailList->subscribe('john@doe.com');

        // Wait for a day
        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        Artisan::call(RunAutomationActionsCommand::class);
        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
        TestTime::addDay()->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        // CampaignAction continues straight to next action after running
        Mail::assertSent(MailcoachMail::class, 1);

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);

        // Unsubscribe the subscriber
        $subscriber->unsubscribe();

        // Wait for 3 days
        TestTime::addDays(3)->addSecond();
        Artisan::call(RunAutomationActionsCommand::class);

        // Mailable was only sent once
        Mail::assertSent(MailcoachMail::class, 1);
        $this->assertNotNull($subscriber->currentActions($automation)->first()->pivot->halted_at);
    }

    /** @test */
    public function it_can_run_automations_twice_with_a_custom_action()
    {
        config()->set(
            'mailcoach.automation.actions.should_run_for_subscriber',
            AllowDuplicateShouldAutomationRunForSubscriberAction::class,
        );

        Mail::fake();

        $emailList = EmailList::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($emailList)
            ->runEvery(CarbonInterval::minute())
            ->triggerOn(new NoTrigger)
            ->chain([
                new AddRandomTagAction(),
                new WaitAction(CarbonInterval::day()),
                new AddRandomTagAction(),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(3, $automation->actions()->count());
        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $john */
        $john = $emailList->subscribe('john@doe.com');

        $automation->run($john);
        $automation->run($john);

        $this->assertEquals(2, $automation->actions()->first()->subscribers->count());

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertEquals(2, $john->tags()->count());

        TestTime::addDay();
        TestTime::addMinute();

        Artisan::call(RunAutomationActionsCommand::class);

        $this->assertEquals(4, $john->tags()->count());
    }

    /** @test */
    public function it_can_run_a_split_automation_twice()
    {
        config()->set(
            'mailcoach.automation.actions.should_run_for_subscriber',
            AllowDuplicateShouldAutomationRunForSubscriberAction::class,
        );
        config()->set(
            'mailcoach.automation.actions.send_automation_mail_to_subscriber',
            AllowDuplicateSendAutomationMailToSubscriberAction::class,
        );

        Mail::fake();

        $emailList = EmailList::factory()->create();

        $automationMail2 = AutomationMail::factory()->create(['subject' => 'Welcome 2']);

        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('Welcome email')
            ->to($emailList)
            ->runEvery(CarbonInterval::minute())
            ->triggerOn(new NoTrigger)
            ->chain([
                new SplitAction(
                    [new SendAutomationMailAction($this->automationMail)],
                    [new SendAutomationMailAction($automationMail2)],
                ),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEquals(1, $automation->actions()->count());
        $this->assertEquals(3, $automation->allActions()->count());
        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $john = $emailList->subscribe('john@doe.com');

        $automation->run($john);
        $automation->run($john);

        $this->assertEquals(2, $automation->actions()->first()->subscribers->count());

        Artisan::call(RunAutomationActionsCommand::class);

        Mail::assertSent(MailcoachMail::class, 4);
    }

    /** @test * */
    public function it_handles_nested_conditions_correctly_when_running_twice()
    {
        config()->set(
            'mailcoach.automation.actions.should_run_for_subscriber',
            AllowDuplicateShouldAutomationRunForSubscriberAction::class,
        );
        config()->set(
            'mailcoach.automation.actions.send_automation_mail_to_subscriber',
            AllowDuplicateSendAutomationMailToSubscriberAction::class,
        );

        TestTime::freeze();

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $automatedMail1 = AutomationMail::factory()->create();
        $automatedMail2 = AutomationMail::factory()->create();
        $automatedMail3 = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->name('Testing nested conditions')
            ->to($emailList)
            ->triggerOn(new NoTrigger)
            ->runEvery(CarbonInterval::minutes(10)) // Run through the automation and check actions every 10 min
            ->chain([
                new ConditionAction(
                    checkFor: CarbonInterval::day(),
                    yesActions: [
                    new ConditionAction(
                        checkFor: CarbonInterval::day(),
                        yesActions: [
                        new SendAutomationMailAction($automatedMail1),
                    ],
                        noActions: [
                        new SendAutomationMailAction($automatedMail2),
                    ],
                        condition: HasTagCondition::class,
                        conditionData: ['tag' => 'yes-tag-2'],
                    ),
                ],
                    noActions: [
                    new SendAutomationMailAction($automatedMail3),
                ],
                    condition: HasTagCondition::class,
                    conditionData: ['tag' => 'yes-tag-1']
                ),
                new WaitAction(CarbonInterval::days(3)),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEquals(6, Action::count());

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber1 */
        $subscriber1 = $automation->emailList->subscribe('subscriber1@example.com');
        $subscriber1->addTags(['yes-tag-1', 'yes-tag-2']); // Should receive mail 1

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber2 */
        $subscriber2 = $automation->emailList->subscribe('subscriber2@example.com');
        $subscriber2->addTags(['yes-tag-1']); // Should receive mail 2

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber3 */
        $subscriber3 = $automation->emailList->subscribe('subscriber3@example.com'); // Should receive mail 3

        $automation->run($subscriber1);
        $automation->run($subscriber2);

        // Run it Twice for subscriber3
        $automation->run($subscriber3);
        $automation->run($subscriber3);

        Artisan::call(RunAutomationActionsCommand::class);

        // Subscriber 1 went straight through all the "yes" paths to the send
        $this->assertEquals(WaitAction::class, $subscriber1->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber1->currentActions($automation)->first()->action->toArray());

        // And has received automationmail 1
        $this->assertSame(1, $subscriber1->sends()->count());
        $this->assertSame($automatedMail1->id, $subscriber1->sends->first()->automationMail->id);

        $this->assertEquals('yes-tag-2', $subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);
        $this->assertEquals('yes-tag-1', $subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);

        TestTime::addDay();

        Artisan::call(RunAutomationActionsCommand::class);

        // Subscriber 2 went through the first yes, and second no
        $this->assertEquals(WaitAction::class, $subscriber2->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber2->currentActions($automation)->first()->action->toArray());

        // And received automationmail 2
        $this->assertSame(1, $subscriber2->sends()->count());
        $this->assertSame($automatedMail2->id, $subscriber2->sends->first()->automationMail->id);

        // Subscriber 3 went through the first no
        $this->assertEquals(WaitAction::class, $subscriber3->currentActions($automation)->first()->action::class);
        $this->assertEquals(['length' => '3', 'unit' => 'days'], $subscriber3->currentActions($automation)->first()->action->toArray());

        // And received automationmail 3 twice
        $this->assertSame(2, $subscriber3->sends()->count());
        $this->assertSame($automatedMail3->id, $subscriber3->sends->first()->automationMail->id);

        // 4 mails were sent in total
        $this->assertEquals(4, Send::count());
    }
}
