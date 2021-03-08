<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Triggers;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class WebhookTriggerTest extends TestCase
{
    use RespondsToApiRequests;

    protected AutomationMail $automationMail;

    protected EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

        $this->emailList = EmailList::factory()->create();
    }

    /** @test * */
    public function it_triggers_when_a_call_is_made_to_an_endpoint()
    {
        Queue::fake();

        TestTime::setTestNow(Carbon::create(2020, 01, 01));

        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new WebhookTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->emailList->subscribe('john@doe.com');

        Artisan::call(RunAutomationTriggersCommand::class);

        $this->assertEmpty($automation->actions->first()->subscribers);

        $subscriber = Subscriber::first();

        $this->loginToApi();

        $this->post(action(TriggerAutomationController::class, [$automation]), [
            'subscribers' => [$subscriber->id],
        ])->assertSuccessful();

        Queue::assertPushed(
            RunAutomationForSubscriberJob::class,
            function (RunAutomationForSubscriberJob $job) use ($subscriber, $automation) {
                $this->assertSame($subscriber->email, $job->subscriber->email);
                $this->assertSame($automation->id, $job->automation->id);

                return true;
            }
        );
    }
}
