<?php

namespace Spatie\Mailcoach\Tests\Domain\Audience;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class UnsubscribeTest extends TestCase
{
    protected Campaign $campaign;

    protected string $mailedUnsubscribeLink;

    protected EmailList $emailList;

    protected Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'html' => '<a href="::unsubscribeUrl::">Unsubscribe</a>',
        ]);

        $this->emailList = $this->campaign->emailList;

        $this->subscriber = $this->campaign->emailList->subscribers->first();
    }

    /** @test */
    public function it_can_render_the_unsubscribe_confirmation_page()
    {
        $this->sendCampaign();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->subscriber->status);

        $this
            ->get($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.unsubscribe');
    }

    /** @test */
    public function it_can_unsubscribe_from_a_list()
    {
        $this->sendCampaign();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->subscriber->status);

        $content = $this
            ->post($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->baseResponse->content();

        $this->assertStringContainsString('unsubscribed', $content);

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $this->subscriber->refresh()->status);

        $this->assertCount(1, CampaignUnsubscribe::all());
        $campaignUnsubscribe = CampaignUnsubscribe::first();

        $this->assertEquals($this->subscriber->uuid, $campaignUnsubscribe->subscriber->uuid);
        $this->assertEquals($this->campaign->uuid, $campaignUnsubscribe->campaign->uuid);

        $subscription = $this->emailList->allSubscribers()->first();
        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscription->status);
    }

    /** @test */
    public function it_will_redirect_to_the_unsubscribed_view_by_default()
    {
        $this->sendCampaign();

        $this
            ->post($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.unsubscribed');
    }

    /** @test */
    public function it_will_redirect_to_the_unsubscribed_url_if_it_has_been_set_on_the_email_list()
    {
        $url = 'https://example.com/unsubscribed';
        $this->campaign->emailList->update(['redirect_after_unsubscribed' => $url]);

        $this->sendCampaign();

        $this
            ->post($this->mailedUnsubscribeLink)
            ->assertRedirect($url);
    }

    /** @test */
    public function it_will_only_store_a_single_unsubscribe_even_if_the_unsubscribe_link_is_used_multiple_times()
    {
        $this->sendCampaign();

        $this->post($this->mailedUnsubscribeLink)->assertSuccessful();
        $response = $this->get($this->mailedUnsubscribeLink)->assertSuccessful()->baseResponse->content();

        $this->assertCount(1, CampaignUnsubscribe::all());

        $this->assertStringContainsString('already unsubscribed', $response);
    }

    /** @test */
    public function the_unsubscribe_will_work_even_if_the_send_is_deleted()
    {
        $this->sendCampaign();

        Send::truncate();

        $this->post($this->mailedUnsubscribeLink)->assertSuccessful();

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $this->subscriber->refresh()->status);
    }

    /** @test */
    public function the_unsubscribe_header_is_added_to_the_email()
    {
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $subscription = $this->emailList->allSubscribers()->first();

            $this->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe'));

            $this->assertEquals('<'.url(action(UnsubscribeController::class, [$subscription->uuid, Send::first()->uuid])).'>', $event->message->getHeaders()->get('List-Unsubscribe')->getValue());

            $this->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe-Post'));

            $this->assertEquals('List-Unsubscribe=One-Click', $event->message->getHeaders()->get('List-Unsubscribe-Post')->getValue());
        });

        dispatch(new SendCampaignJob($this->campaign));
    }

    protected function sendCampaign()
    {
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('a')->first()->attr('href');

            $this->assertStringStartsWith('http://localhost', $link);

            $this->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }
}
