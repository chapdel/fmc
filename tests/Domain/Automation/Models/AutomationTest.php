<?php

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

uses(TestCase::class);
uses(MatchesSnapshots::class);

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);
});

test('the default status is paused', function () {
    $automation = Automation::create();

    test()->assertEquals(AutomationStatus::PAUSED, $automation->status);
});

it('can run a welcome automation', function () {
    Mail::fake();

    $emailList = EmailList::factory()->create();

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('Welcome email')
        ->to($emailList)
        ->runEvery(CarbonInterval::minute())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(1, $automation->actions()->count());
    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    $emailList->subscribe('john@doe.com');

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        test()->assertTrue($mail->hasTo('john@doe.com'));

        return true;
    });
});

test('a halted action will stop the automation', function () {
    Queue::fake();

    $emailList = EmailList::factory()->create();

    $automation = Automation::create()
        ->name('Welcome email')
        ->to($emailList)
        ->runEvery(CarbonInterval::minute())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new HaltAction,
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(2, $automation->actions->count());
    test()->assertEquals(0, $automation->actions->first()->subscribers()->count());

    $emailList->subscribe('john@doe.com');

    test()->processQueuedJobs();

    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Queue::assertNotPushed(SendAutomationMailToSubscriberJob::class);
    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(0, $automation->actions->last()->subscribers()->count());
});

it('continues once the action returns true', function () {
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
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(2, $automation->actions->count());
    test()->assertEquals(0, $automation->actions->first()->subscribers()->count());

    $emailList->subscribe('john@doe.com');

    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertNothingSent();
    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(0, $automation->actions->last()->subscribers()->count());

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class);
    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(1, $automation->actions->last()->subscribers()->count());
});

it('continues when new actions get added', function () {
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
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(2, $automation->actions->count());
    test()->assertEquals(0, $automation->actions->first()->subscribers()->count());

    $emailList->subscribe('john@doe.com');

    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertNothingSent();
    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(0, $automation->actions->last()->subscribers()->count());

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class);
    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(1, $automation->actions->last()->subscribers()->count());

    $newWaitAction = Action::make([
        'uuid' => Str::uuid()->toString(),
    ]);
    $newWaitAction->action = new WaitAction(CarbonInterval::day());

    $automation->chain(array_merge($automation->actions->map->toLivewireArray()->toArray(), [
        $newWaitAction->toLivewireArray(),
    ]));

    $automation = $automation->fresh();

    test()->assertEquals(3, $automation->actions->count());

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(0, $automation->actions->last()->subscribers()->count());

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertEquals(1, $automation->actions->first()->subscribers()->count());
    test()->assertEquals(1, $automation->actions->last()->subscribers()->count());
});

it('can sync actions successfully', function () {
    Queue::fake();

    $emailList = EmailList::factory()->create();

    $automation = Automation::create()
        ->name('Welcome email')
        ->runEvery(CarbonInterval::minute())
        ->to($emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([]);

    test()->assertEquals([], $automation->actions->toArray());

    $waitAction = new WaitAction(CarbonInterval::day());
    $waitAction->uuid = Str::uuid()->toString();

    $automation->chain([
        $waitAction,
    ]);

    test()->assertEquals([
        serialize($waitAction),
    ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

    $firstId = $automation->actions()->first()->id;

    $campaignAction = new SendAutomationMailAction(test()->automationMail);
    $campaignAction->uuid = Str::uuid()->toString();

    $automation->chain([
        $waitAction,
        $campaignAction,
    ]);

    test()->assertEquals([
        serialize($waitAction),
        serialize($campaignAction),
    ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

    test()->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action

    $wait2days = new WaitAction(CarbonInterval::days(2));
    $automation->chain([
        $waitAction,
        $campaignAction,
        $wait2days,
    ]);

    test()->assertEquals([
        serialize($waitAction),
        serialize($campaignAction),
        serialize($wait2days),
    ], $automation->actions()->get()->pluck('action')->map(fn ($action) => serialize($action))->toArray());

    test()->assertEquals($firstId, $automation->actions()->first()->id); // ID hasn't changed, so it didn't delete the action
});

it('can create and run a complicated automation', function () {
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

    test()->refreshServiceProvider();

    test()->assertEquals(1, Automation::count());
    tap(Automation::first(), function (Automation $automation) {
        test()->assertEquals(CarbonInterval::minutes(10), $automation->interval);
        test()->assertInstanceOf(SubscribedTrigger::class, $automation->triggers->first()->getAutomationTrigger());
    });
    test()->assertEquals(8, Action::count());

    test()->assertEquals(2, Action::where('key', 'yesActions')->count());
    test()->assertEquals(1, Action::where('key', 'noActions')->count());

    $subscriber = $automation->emailList->subscribe('john@doe.com');

    // Wait for a day
    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    Artisan::call(RunAutomationActionsCommand::class);
    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // CampaignAction continues straight to next action after running
    Mail::assertSent(MailcoachMail::class, 1);
    test()->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);

    // EnsureTagsExist checks for 3 days
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);
    TestTime::addDays(2)->addSecond();
    test()->assertInstanceOf(ConditionAction::class, $subscriber->currentActions($automation)->first()->action);

    // Adding a tag causes it to go to the next
    $subscriber->addTag('mc::campaign-1-clicked-1');
    Artisan::call(RunAutomationActionsCommand::class);
    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    test()->assertEquals(CarbonInterval::day(), $subscriber->currentActions($automation)->first()->action->interval);

    // Wait for a day
    TestTime::addDays(2)->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // Campaign Action sends again
    Mail::assertSent(MailcoachMail::class, 2);

    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    test()->assertEquals(CarbonInterval::days(3), $subscriber->currentActions($automation)->first()->action->interval);

    // Wait for 3 days
    TestTime::addDays(3)->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // Execute last CampaignAction
    Artisan::call(RunAutomationActionsCommand::class);
    Mail::assertSent(MailcoachMail::class, 3);

    test()->assertInstanceOf(SendAutomationMailAction::class, $subscriber->currentActions($automation)->first()->action);
    test()->assertNotNull($subscriber->currentActions($automation)->first()->pivot->run_at);
});

it('handles nested conditions correctly', function () {
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
            new WaitAction(CarbonInterval::days(3), '3', 'days'),
        ])->start();

    test()->refreshServiceProvider();

    test()->assertEquals(6, Action::count());

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
    test()->assertEquals(WaitAction::class, $subscriber1->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber1->currentActions($automation)->first()->action->toArray());

    // And has received automationmail 1
    test()->assertSame(1, $subscriber1->sends()->count());
    test()->assertSame($automatedMail1->id, $subscriber1->sends->first()->automationMail->id);

    test()->assertEquals('yes-tag-2', $subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);
    test()->assertEquals('yes-tag-1', $subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    // Subscriber 2 went through the first yes, and second no
    test()->assertEquals(WaitAction::class, $subscriber2->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber2->currentActions($automation)->first()->action->toArray());

    // And received automationmail 2
    test()->assertSame(1, $subscriber2->sends()->count());
    test()->assertSame($automatedMail2->id, $subscriber2->sends->first()->automationMail->id);

    // Subscriber 3 went through the first no
    test()->assertEquals(WaitAction::class, $subscriber3->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber3->currentActions($automation)->first()->action->toArray());

    // And received automationmail 3
    test()->assertSame(1, $subscriber3->sends()->count());
    test()->assertSame($automatedMail3->id, $subscriber3->sends->first()->automationMail->id);

    // Only 3 mails were sent in total
    test()->assertEquals(3, Send::count());
});

test('the automation mail can use custom mailable', function () {
    $manager = new QueueManager(test()->app);
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

    test()->refreshServiceProvider();

    test()->assertEquals(1, Action::count());

    $automation->emailList->subscribe('subscriber@example.com');

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

    test()->assertTrue($messages->filter(function (Swift_Message $message) {
        return $message->getSubject() === "This is the subject from the custom mailable.";
    })->count() > 0);
});

it('can run a split automation', function () {
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
                [new SendAutomationMailAction(test()->automationMail)],
                [new SendAutomationMailAction($automationMail2)],
            ),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(1, $automation->actions()->count());
    test()->assertEquals(3, $automation->allActions()->count());
    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    $emailList->subscribe('john@doe.com');

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class, 2);
});

it('will stop the automation if the user is unsubscribed', function () {
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

    test()->refreshServiceProvider();

    test()->assertEquals(1, Automation::count());
    test()->assertEquals(4, Action::count());

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $automation->emailList->subscribe('john@doe.com');

    // Wait for a day
    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    Artisan::call(RunAutomationActionsCommand::class);
    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // CampaignAction continues straight to next action after running
    Mail::assertSent(MailcoachMail::class, 1);

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertInstanceOf(WaitAction::class, $subscriber->currentActions($automation)->first()->action);

    // Unsubscribe the subscriber
    $subscriber->unsubscribe();

    // Wait for 3 days
    TestTime::addDays(3)->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // Mailable was only sent once
    Mail::assertSent(MailcoachMail::class, 1);
    test()->assertNotNull($subscriber->currentActions($automation)->first()->pivot->halted_at);
});

it('can run automations twice with a custom action', function () {
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

    test()->refreshServiceProvider();

    test()->assertEquals(3, $automation->actions()->count());
    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $john */
    $john = $emailList->subscribe('john@doe.com');

    $automation->run($john);
    $automation->run($john);

    test()->assertEquals(2, $automation->actions()->first()->subscribers->count());

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertEquals(2, $john->tags()->count());

    TestTime::addDay();
    TestTime::addMinute();

    Artisan::call(RunAutomationActionsCommand::class);

    test()->assertEquals(4, $john->tags()->count());
});

it('can run a split automation twice', function () {
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
                [new SendAutomationMailAction(test()->automationMail)],
                [new SendAutomationMailAction($automationMail2)],
            ),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->assertEquals(1, $automation->actions()->count());
    test()->assertEquals(3, $automation->allActions()->count());
    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    $john = $emailList->subscribe('john@doe.com');

    $automation->run($john);
    $automation->run($john);

    test()->assertEquals(2, $automation->actions()->first()->subscribers->count());

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class, 4);
});

it('handles nested conditions correctly when running twice', function () {
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
            new WaitAction(CarbonInterval::days(3), '3', 'days'),
        ])->start();

    test()->refreshServiceProvider();

    test()->assertEquals(6, Action::count());

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
    test()->assertEquals(WaitAction::class, $subscriber1->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber1->currentActions($automation)->first()->action->toArray());

    // And has received automationmail 1
    test()->assertSame(1, $subscriber1->sends()->count());
    test()->assertSame($automatedMail1->id, $subscriber1->sends->first()->automationMail->id);

    test()->assertEquals('yes-tag-2', $subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);
    test()->assertEquals('yes-tag-1', $subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag']);

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    // Subscriber 2 went through the first yes, and second no
    test()->assertEquals(WaitAction::class, $subscriber2->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber2->currentActions($automation)->first()->action->toArray());

    // And received automationmail 2
    test()->assertSame(1, $subscriber2->sends()->count());
    test()->assertSame($automatedMail2->id, $subscriber2->sends->first()->automationMail->id);

    // Subscriber 3 went through the first no
    test()->assertEquals(WaitAction::class, $subscriber3->currentActions($automation)->first()->action::class);
    test()->assertEquals(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds], $subscriber3->currentActions($automation)->first()->action->toArray());

    // And received automationmail 3 twice
    test()->assertSame(2, $subscriber3->sends()->count());
    test()->assertSame($automatedMail3->id, $subscriber3->sends->first()->automationMail->id);

    // 4 mails were sent in total
    test()->assertEquals(4, Send::count());
});
