<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Models;

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SendTest extends TestCase
{
    /** @test */
    public function it_can_be_found_by_its_transport_message_id()
    {
        $send = SendFactory::new()->create([
            'transport_message_id' => '1234',
        ]);

        $this->assertTrue($send->is(Send::findByTransportMessageId('1234')));
    }

    /** @test */
    public function it_will_unsubscribe_when_there_is_a_permanent_bounce()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        $campaign = Campaign::factory()->create([
            'email_list_id' => $emailList->id,
        ]);

        $send = SendFactory::new()->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $bouncedAt = now()->subHour();
        $send->registerBounce($bouncedAt);

        $this->assertDatabaseHas('mailcoach_send_feedback_items', [
            'send_id' => $send->id,
            'type' => SendFeedbackType::BOUNCE,
            'created_at' => $bouncedAt,
        ]);

        $this->assertFalse($emailList->isSubscribed($subscriber->email));
    }

    /** @test */
    public function it_can_receive_a_complaint()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        $campaign = Campaign::factory()->create([
            'email_list_id' => $emailList->id,
        ]);

        $send = SendFactory::new()->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $complainedAt = now()->subHour();
        $send->registerComplaint($complainedAt);

        $this->assertDatabaseHas('mailcoach_send_feedback_items', [
            'send_id' => $send->id,
            'type' => SendFeedbackType::COMPLAINT,
            'created_at' => $complainedAt,
        ]);

        $this->assertFalse($emailList->isSubscribed($subscriber->email));
    }

    /** @test */
    public function it_will_not_register_an_open_if_it_was_recently_opened()
    {
        TestTime::freeze();

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_opens' => true]);

        $send->registerOpen();
        $this->assertCount(1, $send->opens()->get());

        TestTime::addSeconds(4);
        $send->registerOpen();
        $this->assertCount(1, $send->opens()->get());

        TestTime::addSeconds(1);
        $send->registerOpen();
        $this->assertCount(2, $send->opens()->get());
    }

    /** @test */
    public function it_will_register_an_open_at_a_specific_time()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_opens' => true]);

        $openedAt = now()->subHour()->setMicroseconds(0);

        $send->registerOpen($openedAt);

        $this->assertEquals($openedAt, $send->opens()->first()->created_at);
    }

    /** @test */
    public function it_will_not_register_a_click_of_an_unsubscribe_link()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $unsubscribeUrl = $send->subscriber->unsubscribeUrl($send);

        $send->registerClick($unsubscribeUrl);

        $this->assertCount(0, $send->clicks()->get());
    }

    /** @test */
    public function it_can_register_a_click_at_a_given_time()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $clickedAt = now()->subDay()->setMilliseconds(0);
        $send->registerClick('https://example.com', $clickedAt);

        $this->assertCount(1, $send->clicks()->get());
        $this->assertEquals($clickedAt, $send->clicks()->first()->created_at);
    }

    /** @test */
    public function it_can_register_a_click_and_strips_utm_tags()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $clickedAt = now()->subDay()->setMilliseconds(0);
        $send->registerClick('https://example.com?utm_campaign=My+campaign', $clickedAt);

        $this->assertCount(1, $send->clicks()->get());
        $this->assertEquals($clickedAt, $send->clicks()->first()->created_at);
        $this->assertEquals('https://example.com', $send->clicks()->first()->link->url);
    }

    /** @test */
    public function registering_clicks_will_update_the_click_count()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $anotherSubscriber */
        $anotherSubscriber = Subscriber::factory()->create(['email_list_id' => $emailList->id]);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $campaign = Campaign::factory()->create([
            'email_list_id' => $emailList->id,
            'track_clicks' => true,
        ]);

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $anotherSend */
        $anotherSend = SendFactory::new()->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $anotherSubscriber->id,
        ]);

        $linkA = 'https://mailcoach.app';
        $linkB = 'https://spatie.be';

        $campaignClick = $send->registerClick($linkA);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink $campaignLinkA */
        $campaignLinkA = $campaignClick->link;

        $this->assertEquals(1, $campaignLinkA->click_count);
        $this->assertEquals(1, $campaignLinkA->unique_click_count);

        $send->registerClick($linkA);
        $this->assertEquals(2, $campaignLinkA->refresh()->click_count);
        $this->assertEquals(1, $campaignLinkA->refresh()->unique_click_count);

        $anotherSend->registerClick($linkA);
        $this->assertEquals(3, $campaignLinkA->refresh()->click_count);
        $this->assertEquals(2, $campaignLinkA->refresh()->unique_click_count);

        $anotherSend->registerClick($linkA);
        $this->assertEquals(4, $campaignLinkA->refresh()->click_count);
        $this->assertEquals(2, $campaignLinkA->refresh()->unique_click_count);

        $campaignClick = $send->registerClick($linkB);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink $campaignLinkA */
        $campaignLinkB = $campaignClick->link;

        $this->assertEquals(1, $campaignLinkB->click_count);
        $this->assertEquals(1, $campaignLinkB->unique_click_count);

        $send->registerClick($linkB);
        $this->assertEquals(2, $campaignLinkB->refresh()->click_count);
        $this->assertEquals(1, $campaignLinkB->refresh()->unique_click_count);
    }
}
