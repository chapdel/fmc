<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Carbon\CarbonInterval;
use Illuminate\Queue\Connectors\SyncConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class TriggerAutomationControllerTest extends TestCase
{
    use RespondsToApiRequests;

    protected AutomationMail $automationMail;

    protected EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->automationMail = AutomationMail::factory()->create([
            'subject' => 'Welcome',
        ]);

        $this->emailList = EmailList::factory()->create();
    }

    /** @test * */
    public function it_responds_with_200()
    {
        $this->withExceptionHandling();

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new WebhookTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $subscriber = $this->emailList->subscribe('john@doe.com');

        $this->postJson(action(TriggerAutomationController::class, [$automation]), [
            'subscribers' => [$subscriber->id],
        ])->assertStatus(200);
    }

    /** @test * */
    public function it_needs_an_automation_with_a_webhook_trigger()
    {
        $this->withExceptionHandling();

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new DateTrigger(now()))
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $subscriber = $this->emailList->subscribe('john@doe.com');

        $this->postJson(action(TriggerAutomationController::class, [$automation]), [
            'subscribers' => [$subscriber->id],
        ])->assertStatus(400)
          ->assertSee('This automation does not have a Webhook trigger.');
    }

    /** @test * */
    public function it_only_handles_subscribers_from_the_email_list()
    {
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new WebhookTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $subscriber1 = $this->emailList->subscribe('john@doe.com');
        $subscriber2 = SubscriberFactory::new()->create();

        $this->postJson(action(TriggerAutomationController::class, [$automation]), [
            'subscribers' => [$subscriber1->id, $subscriber2->id],
        ])->assertSuccessful();

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }

    /** @test * */
    public function it_needs_a_subscribed_subscriber()
    {
        $manager = new QueueManager($this->app);
        $manager->addConnector('sync', function () {
            return new SyncConnector();
        });
        Queue::swap($manager);

        $this->withExceptionHandling();

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new WebhookTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $subscriber1 = $this->emailList->subscribe('john1@doe.com');
        $subscriber2 = $this->emailList->subscribe('john2@doe.com');
        $subscriber2->unsubscribe();

        $this->postJson(action(TriggerAutomationController::class, [$automation]), [
            'subscribers' => [$subscriber1->id, $subscriber2->id],
        ])->assertSuccessful();

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }
}