<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\PublicApi;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Front\Controllers\SubscribeController;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\Tag;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class SubscribeControllerTest extends TestCase
{
    private EmailList $emailList;

    private ?string $confirmSubscriptionLink;

    private $email = 'info@spatie.be';

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->emailList = EmailList::factory()->create([
            'requires_confirmation' => false,
            'allow_form_subscriptions' => true,
            'redirect_after_subscribed' => 'https://example.com/redirect-after-subscribed',
            'redirect_after_already_subscribed' => 'https://example.com/redirect-after-already-subscribed',
            'redirect_after_subscription_pending' => 'https://example.com/redirect-after-subscription-pending',
            'redirect_after_unsubscribed' => 'https://example.com/redirect-after-unsubscribed',
        ]);
    }

    /** @test */
    public function it_can_subscribe_to_an_email_list_without_double_opt_in()
    {
        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects())
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_subscribed']);

        $this->assertEquals(
            SubscriptionStatus::SUBSCRIBED,
            $this->emailList->getSubscriptionStatus($this->email)
        );
    }

    /** @test */
    public function when_not_specified_on_the_form_it_will_redirect_to_the_redirect_after_subscribed_url_on_the_list()
    {
        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertRedirect($this->emailList->redirect_after_subscribed);
    }

    /** @test */
    public function when_no_redirect_after_subscribed_is_specified_on_the_request_or_email_list_it_will_redirect_show_a_view()
    {
        $this->withoutExceptionHandling();

        $this->emailList->update(['redirect_after_subscribed' => null]);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.subscribed');
    }

    /** @test */
    public function it_will_return_a_not_found_response_for_email_list_that_do_not_allow_form_subscriptions()
    {
        $this->emailList->update(['allow_form_subscriptions' => false]);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects())
            ->assertStatus(404);
    }

    /** @test */
    public function it_can_accept_a_first_and_last_name()
    {
        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]))
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_subscribed']);

        $subscriber = Subscriber::where('email', $this->payloadWithRedirects()['email'])->first();

        $this->assertEquals('John', $subscriber->first_name);
        $this->assertEquals('Doe', $subscriber->last_name);
    }

    /** @test */
    public function it_can_accept_attributes()
    {
        $this->withoutExceptionHandling();

        $this->emailList->allowed_form_extra_attributes = 'attribute1;attribute2';
        $this->emailList->save();

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects([
                'attributes' => [
                    'attribute1' => 'foo',
                    'attribute2' => 'bar',
                    'attribute3' => 'forbidden',
                ],
            ]))
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_subscribed']);

        $subscriber = Subscriber::where('email', $this->payloadWithRedirects()['email'])->first();

        $this->assertEquals('foo', $subscriber->extra_attributes->attribute1);
        $this->assertEmpty($subscriber->extra_attributes->attribute3);
        $this->assertEquals('bar', $subscriber->extra_attributes->attribute2);
    }

    /** @test */
    public function it_can_accept_tags()
    {
        $test1Tag = Tag::create(['name' => 'test1', 'email_list_id' => $this->emailList->id]);
        $test2Tag = Tag::create(['name' => 'test2', 'email_list_id' => $this->emailList->id]);
        $test3Tag = Tag::create(['name' => 'test3', 'email_list_id' => $this->emailList->id]);

        $this->emailList->allowedFormSubscriptionTags()->sync([$test1Tag->id, $test3Tag->id]);

        $this
            ->post(
                action(SubscribeController::class, $this->emailList->uuid),
                $this->payloadWithRedirects([
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'tags' => 'test1;test2;test3',
                ])
            );

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = Subscriber::where('email', $this->payloadWithRedirects()['email'])->first();

        $this->assertEquals(['test1', 'test3'], $subscriber->tags()->pluck('name')->toArray());
    }

    /** @test */
    public function it_will_redirect_to_the_correct_url_if_the_email_address_is_already_subscribed()
    {
        $this->emailList->subscribe($this->payloadWithRedirects()['email']);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects())
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_already_subscribed']);
    }

    /** @test */
    public function it_will_add_tags_if_the_email_address_is_already_subscribed()
    {
        $tag1 = Tag::create(['name' => 'test1', 'email_list_id' => $this->emailList->id]);
        $tag2 = Tag::create(['name' => 'test2', 'email_list_id' => $this->emailList->id]);
        $tag3 = Tag::create(['name' => 'test3', 'email_list_id' => $this->emailList->id]);

        $this->emailList->allowedFormSubscriptionTags()->sync([$tag1->id, $tag2->id, $tag3->id]);

        $this->emailList->subscribe($this->payloadWithRedirects()['email']);
        $subscriber = Subscriber::findForEmail($this->payloadWithRedirects()['email'], $this->emailList);
        $subscriber->addTags(['test1', 'test2']);

        $this->assertEquals(2, $subscriber->fresh()->tags()->count());

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects([
                'tags' => 'test3',
            ]))
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_already_subscribed']);

        $this->assertEquals(3, $subscriber->tags()->count());
    }

    /** @test */
    public function when_not_specified_on_the_form_it_will_redirect_to_the_redirect_after_already_subscribed_url_on_the_list()
    {
        $this->withoutExceptionHandling();

        $this->emailList->subscribe($this->payload()['email']);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertRedirect($this->emailList->redirect_after_already_subscribed);
    }

    /** @test */
    public function when_no_redirect_after_already_subscribed_is_specified_on_the_request_or_email_list_it_will_redirect_show_a_view()
    {
        $this->emailList->subscribe($this->payload()['email']);

        $this->emailList->update(['redirect_after_already_subscribed' => null]);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.alreadySubscribed');
    }

    /** @test */
    public function it_will_redirect_to_the_correct_url_if_the_subscription_is_pending()
    {
        $this->emailList->update(['requires_confirmation' => true]);

        $this->emailList->subscribe($this->payloadWithRedirects()['email']);

        $redirectUrl = 'https://mydomain/subscription-pending';

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects(
                ['redirect_after_subscription_pending' => $redirectUrl]
            ))
            ->assertRedirect($redirectUrl);
    }

    /** @test */
    public function when_not_specified_on_the_form_it_will_redirect_to_the_redirect_after_subscription_pending_url_on_the_list()
    {
        $this->emailList->update(['requires_confirmation' => true]);
        $this->emailList->subscribe($this->payload()['email']);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertRedirect($this->emailList->redirect_after_subscription_pending);
    }

    /** @test */
    public function when_no_redirect_after_subscription_pending_is_specified_on_the_request_or_email_list_it_will_redirect_show_a_view()
    {
        $this->withoutExceptionHandling();

        $this->emailList->update(['requires_confirmation' => true]);
        $this->emailList->subscribe($this->payload()['email']);

        $this->emailList->update(['redirect_after_subscription_pending' => null]);

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payload())
            ->assertSuccessful()
            ->assertViewIs('mailcoach::landingPages.confirmSubscription');
    }

    /** @test */
    public function clicking_the_link_in_the_confirm_subscription_mail_will_redirect_to_the_given_url()
    {
        $this->emailList->update(['requires_confirmation' => true]);

        /*
         * We'll grab the url behind the confirm subscription button in the mail that will be sent
         */
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $this->confirmSubscriptionLink = (new Crawler($event->message->getBody()))
                ->filter('.button-primary')->first()->attr('href');
        });

        $this
            ->post(action(SubscribeController::class, $this->emailList->uuid), $this->payloadWithRedirects())
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_subscription_pending']);

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = Subscriber::where('email', $this->payloadWithRedirects()['email'])->first();
        $this->assertEquals(SubscriptionStatus::UNCONFIRMED, $subscriber->refresh()->status);

        /*
         * We'll pretend the user clicked the confirm subscription button by visiting the url
         */
        $this
            ->get($this->confirmSubscriptionLink)
            ->assertRedirect($this->payloadWithRedirects()['redirect_after_subscribed']);
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
    }

    public function payload(array $extraAttributes = [])
    {
        return array_merge([
            'email' => $this->email,
        ], $extraAttributes);
    }

    protected function payloadWithRedirects(array $extraAttributes = []): array
    {
        return array_merge([
            'redirect_after_subscribed' => 'https://mydomain/subscribed',
            'redirect_after_already_subscribed' => 'https://mydomain/already-subscribed',
            'redirect_after_subscription_pending' => 'https://mydomain/subscription-pending',
        ], $this->payload($extraAttributes));
    }
}
