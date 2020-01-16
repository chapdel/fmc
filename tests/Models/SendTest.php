<?php

namespace Spatie\Mailcoach\Tests\Models;

use Spatie\Mailcoach\Enums\SendFeedbackType;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SendTest extends TestCase
{
    /** @test */
    public function it_can_be_found_by_its_transport_message_id()
    {
        $send = factory(Send::class)->create([
            'transport_message_id' => '1234',
        ]);

        $this->assertTrue($send->is(Send::findByTransportMessageId('1234')));
    }

    /** @test */
    public function it_will_unsubscribe_when_there_is_a_permanent_bounce()
    {
        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = factory(Subscriber::class)->create();

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        $campaign = factory(Campaign::class)->create([
            'email_list_id' => $emailList->id,
        ]);

        $send = factory(Send::class)->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $send->registerBounce();

        $this->assertDatabaseHas('mailcoach_send_feedback_items', [
            'send_id' => $send->id,
            'type' => SendFeedbackType::BOUNCE
        ]);

        $this->assertFalse($emailList->isSubscribed($subscriber->email));
    }

    /** @test */
    public function it_can_receive_a_complaint()
    {
        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = factory(Subscriber::class)->create();

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        $campaign = factory(Campaign::class)->create([
            'email_list_id' => $emailList->id,
        ]);

        $send = factory(Send::class)->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $send->registerComplaint();

        $this->assertDatabaseHas('mailcoach_send_feedback_items', [
            'send_id' => $send->id,
            'type' => SendFeedbackType::COMPLAINT
        ]);

        $this->assertFalse($emailList->isSubscribed($subscriber->email));
    }

    /** @test */
    public function it_will_not_register_an_open_if_it_was_recently_opened()
    {
        TestTime::freeze();

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();
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
    public function it_will_not_register_a_click_of_an_unsubscribe_link()
    {
        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();
        $send->campaign->update(['track_clicks' => true]);

        $unsubscribeUrl = $send->subscriber->unsubscribeUrl($send);

        $send->registerClick($unsubscribeUrl);

        $this->assertCount(0, $send->clicks()->get());
    }

    /** @test */
    public function registering_clicks_will_update_the_click_count()
    {
        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = factory(Subscriber::class)->create();

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = $subscriber->emailList;

        /** @var \Spatie\Mailcoach\Models\Subscriber $anotherSubscriber */
        $anotherSubscriber = factory(Subscriber::class)->create(['email_list_id' => $emailList->id]);

        /** @var \Spatie\Mailcoach\Models\Campaign $campaign */
        $campaign = factory(Campaign::class)->create([
            'email_list_id' => $emailList->id,
            'track_clicks' => true,
        ]);

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
        ]);

        /** @var \Spatie\Mailcoach\Models\Send $anotherSend */
        $anotherSend = factory(Send::class)->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $anotherSubscriber->id,
        ]);

        $linkA = 'https://mailcoach.app';
        $linkB = 'https://spatie.be';

        $campaignClick = $send->registerClick($linkA);

        /** @var \Spatie\Mailcoach\Models\CampaignLink $campaignLinkA */
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

        /** @var \Spatie\Mailcoach\Models\CampaignLink $campaignLinkA */
        $campaignLinkB = $campaignClick->link;

        $this->assertEquals(1, $campaignLinkB->click_count);
        $this->assertEquals(1, $campaignLinkB->unique_click_count);

        $send->registerClick($linkB);
        $this->assertEquals(2, $campaignLinkB->refresh()->click_count);
        $this->assertEquals(1, $campaignLinkB->refresh()->unique_click_count);
    }
}
