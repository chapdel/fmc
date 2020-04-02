<?php

namespace Spatie\Mailcoach\Tests\Jobs;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class MarkCampaignAsSentJobTest extends TestCase
{
    use MatchesSnapshots;

    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())
            ->withSubscriberCount(3)
            ->create();

        $this->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

        Mail::fake();

        Event::fake();
    }

    /** @test */
    public function it_marks_a_campaign_as_sent_and_sends_an_event()
    {
        dispatch(new MarkCampaignAsSentJob($this->campaign));

        Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) {
            $this->assertEquals($this->campaign->id, $event->campaign->id);

            return true;
        });

        $this->campaign->refresh();
        $this->assertEquals(CampaignStatus::SENT, $this->campaign->status);
    }

    /** @test * */
    public function it_does_nothing_if_the_amount_of_current_sends_doesnt_match_the_intended_sends_and_releases()
    {
        $this->campaign->update(['sent_to_number_of_subscribers' => 1]);

        Queue::after(function (JobProcessed $event) {
            $this->assertTrue($event->job->isReleased());
            $this->assertEquals(1, $event->job->attempts());
        });

        dispatch(new MarkCampaignAsSentJob($this->campaign));

        Event::assertNotDispatched(CampaignSentEvent::class);
        $this->campaign->refresh();
        $this->assertEquals(CampaignStatus::DRAFT, $this->campaign->status);
    }
}
