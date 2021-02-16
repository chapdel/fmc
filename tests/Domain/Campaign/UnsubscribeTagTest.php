<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class UnsubscribeTagTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    private Campaign $campaign;

    /** @var string */
    private string $mailedUnsubscribeLink;

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList */
    private EmailList $emailList;

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber */
    private Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'html' => '<a href="::unsubscribeTag::some tag::">Unsubscribe</a>',
        ]);

        $this->emailList = $this->campaign->emailList;

        $this->subscriber = $this->campaign->emailList->subscribers->first();
        $this->subscriber->addTag('some tag');
    }

    /** @test */
    public function it_can_render_the_unsubscribe_confirmation_page()
    {
        $this->sendCampaign();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->subscriber->status);

        $this
            ->get($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.unsubscribe-tag');
    }

    /** @test */
    public function it_can_unsubscribe_from_a_tag()
    {
        $this->sendCampaign();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->subscriber->status);

        $this->assertTrue($this->subscriber->hasTag('some tag'));

        $content = $this
            ->post($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->baseResponse->content();

        $this->assertStringContainsString('unsubscribed', $content);

        $this->assertFalse($this->subscriber->fresh()->hasTag('some tag'));
    }

    /** @test */
    public function it_will_redirect_to_the_unsubscribed_view_by_default()
    {
        $this->sendCampaign();

        $this
            ->post($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.unsubscribed-tag');
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
    public function the_unsubscribe_will_work_even_if_the_send_is_deleted()
    {
        $this->sendCampaign();

        Send::truncate();

        $this->post($this->mailedUnsubscribeLink)->assertSuccessful();

        $this->assertFalse($this->subscriber->fresh()->hasTag('some tag'));
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
