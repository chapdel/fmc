<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Front\Controllers\SubscribeController;
use Symfony\Component\DomCrawler\Crawler;

beforeEach(function () {
    test()->withExceptionHandling();

    test()->emailList = EmailList::factory()->create([
        'requires_confirmation' => false,
        'allow_form_subscriptions' => true,
        'redirect_after_subscribed' => 'https://example.com/redirect-after-subscribed',
        'redirect_after_already_subscribed' => 'https://example.com/redirect-after-already-subscribed',
        'redirect_after_subscription_pending' => 'https://example.com/redirect-after-subscription-pending',
        'redirect_after_unsubscribed' => 'https://example.com/redirect-after-unsubscribed',
    ]);

    test()->email = 'info@spatie.be';
});

it('can confirm a subscriber', function () {
    $payloadWithRedirects = [
        'email' => test()->email,
        'redirect_after_subscribed' => 'https://mydomain/subscribed',
        'redirect_after_already_subscribed' => 'https://mydomain/already-subscribed',
    ];

    test()->emailList->update(['requires_confirmation' => true, 'redirect_after_subscription_pending' => null]);

    /*
     * We'll grab the url behind the confirm subscription button in the mail that will be sent
     */
    Event::listen(MessageSent::class, function (MessageSent $event) {
        test()->confirmSubscriptionLink = (new Crawler($event->message->getHtmlBody()))
            ->filter('.button-primary')->first()->attr('href');
    });

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), $payloadWithRedirects);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::findForEmail($payloadWithRedirects['email'], test()->emailList);
    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Unconfirmed);

    /*
     * We'll pretend the user clicked the confirm subscription button by visiting the url
     */
    $this
        ->get(test()->confirmSubscriptionLink)
        ->assertRedirect($payloadWithRedirects['redirect_after_subscribed']);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Subscribed);
});

it('responds with could not find response', function () {
    $this->get(action(\Spatie\Mailcoach\Http\Front\Controllers\ConfirmSubscriberController::class, 'non-existing-subscriber'))
        ->assertSuccessful()
        ->assertSee('We could not find your subscription to this list');
});
