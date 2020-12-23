<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Http\Front\Controllers\ConfirmSubscriberController;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class DoubleOptInTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    private EmailList $emailList;

    /** @var string */
    private string $mailedLink;

    public function setUp(): void
    {
        parent::setUp();

        /* @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList $emailList */
        $this->emailList = EmailList::factory()->create(['requires_confirmation' => true]);

        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('.button-primary')->first()->attr('href');

            $this->mailedLink = Str::after($link, 'http://localhost');
        });

        $this->emailList->subscribe('john@example.com');
    }

    /** @test */
    public function when_subscribing_to_a_double_opt_in_list_a_click_in_the_confirmation_mail_is_needed_to_subscribe()
    {
        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));

        $this
            ->get($this->mailedLink)
            ->assertSuccessful();

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
    }

    /** @test */
    public function clicking_the_mailed_link_twice_will_not_result_in_a_double_subscription()
    {
        $this
            ->get($this->mailedLink)
            ->assertSuccessful();

        $this
            ->get($this->mailedLink)
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.alreadySubscribed');

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
        $this->assertEquals(1, Subscriber::count());
    }

    /** @test */
    public function clicking_on_an_invalid_link_will_render_to_correct_response()
    {
        $content = $this
            ->get(action(ConfirmSubscriberController::class, 'invalid-uuid'))
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.couldNotFindSubscription');
    }
}
