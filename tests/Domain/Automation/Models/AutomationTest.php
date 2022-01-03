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
use Spatie\Mailcoach\Tests\TestClasses\AddRandomTagAction;
use Spatie\Mailcoach\Tests\TestClasses\AllowDuplicateSendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\AllowDuplicateShouldAutomationRunForSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);
});

test('the default status is paused', function () {
    $automation = Automation::create();

    expect($automation->status)->toEqual(AutomationStatus::PAUSED);
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

    expect($automation->actions()->count())->toEqual(1);
    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    $emailList->subscribe('john@doe.com');

    expect($automation->actions()->first()->subscribers->count())->toEqual(1);

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->hasTo('john@doe.com'))->toBeTrue();

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

    expect($automation->actions->count())->toEqual(2);
    expect($automation->actions->first()->subscribers()->count())->toEqual(0);

    $emailList->subscribe('john@doe.com');

    test()->processQueuedJobs();

    expect($automation->actions->first()->subscribers()->count())->toEqual(1);

    Artisan::call(RunAutomationActionsCommand::class);

    Queue::assertNotPushed(SendAutomationMailToSubscriberJob::class);
    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(0);
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

    expect($automation->actions->count())->toEqual(2);
    expect($automation->actions->first()->subscribers()->count())->toEqual(0);

    $emailList->subscribe('john@doe.com');

    expect($automation->actions->first()->subscribers()->count())->toEqual(1);

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertNothingSent();
    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(0);

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class);
    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(1);
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

    expect($automation->actions->count())->toEqual(2);
    expect($automation->actions->first()->subscribers()->count())->toEqual(0);

    $emailList->subscribe('john@doe.com');

    expect($automation->actions->first()->subscribers()->count())->toEqual(1);

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertNothingSent();
    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(0);

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    Mail::assertSent(MailcoachMail::class);
    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(1);

    $newWaitAction = Action::make([
        'uuid' => Str::uuid()->toString(),
    ]);
    $newWaitAction->action = new WaitAction(CarbonInterval::day());

    $automation->chain(array_merge($automation->actions->map->toLivewireArray()->toArray(), [
        $newWaitAction->toLivewireArray(),
    ]));

    $automation = $automation->fresh();

    expect($automation->actions->count())->toEqual(3);

    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(0);

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    expect($automation->actions->first()->subscribers()->count())->toEqual(1);
    expect($automation->actions->last()->subscribers()->count())->toEqual(1);
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

    expect($automation->actions->toArray())->toEqual([]);

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

    expect($automation->actions()->first()->id)->toEqual($firstId); // ID hasn't changed, so it didn't delete the action

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

    expect($automation->actions()->first()->id)->toEqual($firstId); // ID hasn't changed, so it didn't delete the action
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

    expect(Automation::count())->toEqual(1);
    tap(Automation::first(), function (Automation $automation) {
        expect($automation->interval)->toEqual(CarbonInterval::minutes(10));
        expect($automation->triggers->first()->getAutomationTrigger())->toBeInstanceOf(SubscribedTrigger::class);
    });
    expect(Action::count())->toEqual(8);

    expect(Action::where('key', 'yesActions')->count())->toEqual(2);
    expect(Action::where('key', 'noActions')->count())->toEqual(1);

    $subscriber = $automation->emailList->subscribe('john@doe.com');

    // Wait for a day
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    Artisan::call(RunAutomationActionsCommand::class);
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // CampaignAction continues straight to next action after running
    Mail::assertSent(MailcoachMail::class, 1);
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(ConditionAction::class);

    // EnsureTagsExist checks for 3 days
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(ConditionAction::class);
    TestTime::addDays(2)->addSecond();
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(ConditionAction::class);

    // Adding a tag causes it to go to the next
    $subscriber->addTag('mc::campaign-1-clicked-1');
    Artisan::call(RunAutomationActionsCommand::class);
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    expect($subscriber->currentActions($automation)->first()->action->interval)->toEqual(CarbonInterval::day());

    // Wait for a day
    TestTime::addDays(2)->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // Campaign Action sends again
    Mail::assertSent(MailcoachMail::class, 2);

    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    expect($subscriber->currentActions($automation)->first()->action->interval)->toEqual(CarbonInterval::days(3));

    // Wait for 3 days
    TestTime::addDays(3)->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // Execute last CampaignAction
    Artisan::call(RunAutomationActionsCommand::class);
    Mail::assertSent(MailcoachMail::class, 3);

    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(SendAutomationMailAction::class);
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
                            new ConditionAction(
                                checkFor: CarbonInterval::day(),
                                yesActions: [
                                    new SendAutomationMailAction($automatedMail1),
                                ],
                                noActions: [],
                                condition: HasTagCondition::class,
                                conditionData: ['tag' => 'yes-tag-3'],
                            )
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

    expect(Action::count())->toEqual(7);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber1 */
    $subscriber1 = $automation->emailList->subscribe('subscriber1@example.com');
    $subscriber1->addTags(['yes-tag-1', 'yes-tag-2', 'yes-tag-3']); // Should receive mail 1

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber2 */
    $subscriber2 = $automation->emailList->subscribe('subscriber2@example.com');
    $subscriber2->addTags(['yes-tag-1']); // Should receive mail 2

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber3 */
    $subscriber3 = $automation->emailList->subscribe('subscriber3@example.com'); // Should receive mail 3

    Artisan::call(RunAutomationActionsCommand::class);

    // Subscriber 1 went straight through all the "yes" paths to the send
    expect($subscriber1->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber1->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And has received automationmail 1
    expect($subscriber1->sends()->count())->toBe(1);
    expect($subscriber1->sends->first()->automationMail->id)->toBe($automatedMail1->id);

    expect($subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag'])->toEqual('yes-tag-2');
    expect($subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag'])->toEqual('yes-tag-1');

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    // Subscriber 2 went through the first yes, and second no
    expect($subscriber2->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber2->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And received automationmail 2
    expect($subscriber2->sends()->count())->toBe(1);
    expect($subscriber2->sends->first()->automationMail->id)->toBe($automatedMail2->id);

    // Subscriber 3 went through the first no
    expect($subscriber3->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber3->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And received automationmail 3
    expect($subscriber3->sends()->count())->toBe(1);
    expect($subscriber3->sends->first()->automationMail->id)->toBe($automatedMail3->id);

    // Only 3 mails were sent in total
    expect(Send::count())->toEqual(3);
});

test('the automation mail can use custom mailable', function () {
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

    test()->refreshServiceProvider();

    expect(Action::count())->toEqual(1);

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

    expect($automation->actions()->count())->toEqual(1);
    expect($automation->allActions()->count())->toEqual(3);
    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    $emailList->subscribe('john@doe.com');

    expect($automation->actions()->first()->subscribers->count())->toEqual(1);

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

    expect(Automation::count())->toEqual(1);
    expect(Action::count())->toEqual(4);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $automation->emailList->subscribe('john@doe.com');

    // Wait for a day
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    Artisan::call(RunAutomationActionsCommand::class);
    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);
    TestTime::addDay()->addSecond();
    Artisan::call(RunAutomationActionsCommand::class);

    // CampaignAction continues straight to next action after running
    Mail::assertSent(MailcoachMail::class, 1);

    Artisan::call(RunAutomationActionsCommand::class);

    expect($subscriber->currentActions($automation)->first()->action)->toBeInstanceOf(WaitAction::class);

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

    expect($automation->actions()->count())->toEqual(3);
    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $john */
    $john = $emailList->subscribe('john@doe.com');

    $automation->run($john);
    $automation->run($john);

    expect($automation->actions()->first()->subscribers->count())->toEqual(2);

    Artisan::call(RunAutomationActionsCommand::class);

    expect($john->tags()->count())->toEqual(2);

    TestTime::addDay();
    TestTime::addMinute();

    Artisan::call(RunAutomationActionsCommand::class);

    expect($john->tags()->count())->toEqual(4);
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

    expect($automation->actions()->count())->toEqual(1);
    expect($automation->allActions()->count())->toEqual(3);
    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    $john = $emailList->subscribe('john@doe.com');

    $automation->run($john);
    $automation->run($john);

    expect($automation->actions()->first()->subscribers->count())->toEqual(2);

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

    expect(Action::count())->toEqual(6);

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
    expect($subscriber1->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber1->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And has received automationmail 1
    expect($subscriber1->sends()->count())->toBe(1);
    expect($subscriber1->sends->first()->automationMail->id)->toBe($automatedMail1->id);

    expect($subscriber2->currentActions($automation)->first()->action->toArray()['conditionData']['tag'])->toEqual('yes-tag-2');
    expect($subscriber3->currentActions($automation)->first()->action->toArray()['conditionData']['tag'])->toEqual('yes-tag-1');

    TestTime::addDay();

    Artisan::call(RunAutomationActionsCommand::class);

    // Subscriber 2 went through the first yes, and second no
    expect($subscriber2->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber2->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And received automationmail 2
    expect($subscriber2->sends()->count())->toBe(1);
    expect($subscriber2->sends->first()->automationMail->id)->toBe($automatedMail2->id);

    // Subscriber 3 went through the first no
    expect($subscriber3->currentActions($automation)->first()->action::class)->toEqual(WaitAction::class);
    expect($subscriber3->currentActions($automation)->first()->action->toArray())->toEqual(['length' => '3', 'unit' => 'days', 'seconds' => CarbonInterval::days(3)->totalSeconds]);

    // And received automationmail 3 twice
    expect($subscriber3->sends()->count())->toBe(2);
    expect($subscriber3->sends->first()->automationMail->id)->toBe($automatedMail3->id);

    // 4 mails were sent in total
    expect(Send::count())->toEqual(4);
});
