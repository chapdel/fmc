<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
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

it('can subscribe to an email list without double opt in', function () {
    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList), payloadWithRedirects())
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    test()->assertEquals(
        SubscriptionStatus::Subscribed,
        test()->emailList->getSubscriptionStatus(test()->email)
    );
});

it('can subscribe to an email list with a honeypot', function () {
    test()->emailList->update(['honeypot_field' => 'username']);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList), payloadWithRedirects([
            'username' => '',
        ]))
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    test()->assertEquals(
        SubscriptionStatus::Subscribed,
        test()->emailList->getSubscriptionStatus(test()->email)
    );
});

it('will fake a successful response when the honeypot is filled in', function () {
    test()->emailList->update(['honeypot_field' => 'username']);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList), payloadWithRedirects([
            'username' => 'some-username',
        ]))
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    expect(Subscriber::findForEmail(test()->email, test()->emailList))->toBeNull();
});

test('when not specified on the form it will redirect to the redirect after subscribed url on the list', function () {
    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertRedirect(test()->emailList->redirect_after_subscribed);
});

test('when no redirect after subscribed is specified on the request or email list it will redirect show a view', function () {
    test()->withoutExceptionHandling();

    test()->emailList->update(['redirect_after_subscribed' => null]);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.subscribed');
});

it('will return a not found response for email list that do not allow form subscriptions', function () {
    test()->emailList->update(['allow_form_subscriptions' => false]);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects())
        ->assertStatus(404);
});

it('can accept a first and last name', function () {
    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]))
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    $subscriber = Subscriber::findForEmail(payloadWithRedirects()['email'], test()->emailList);

    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');
});

it('can accept attributes', function () {
    test()->withoutExceptionHandling();

    test()->emailList->allowed_form_extra_attributes = 'attribute1,attribute2';
    test()->emailList->save();

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects([
            'attributes' => [
                'attribute1' => 'foo',
                'attribute2' => 'bar',
                'attribute3' => 'forbidden',
            ],
        ]))
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    $subscriber = Subscriber::findForEmail(payloadWithRedirects()['email'], test()->emailList);

    expect($subscriber->extra_attributes->attribute1)->toEqual('foo');
    expect($subscriber->extra_attributes->attribute3)->toBeEmpty();
    expect($subscriber->extra_attributes->attribute2)->toEqual('bar');
});

it('can accept tags', function () {
    $test1Tag = Tag::create(['name' => 'test1', 'email_list_id' => test()->emailList->id]);
    $test2Tag = Tag::create(['name' => 'test2', 'email_list_id' => test()->emailList->id]);
    $test3Tag = Tag::create(['name' => 'test3', 'email_list_id' => test()->emailList->id]);

    test()->emailList->allowedFormSubscriptionTags()->sync([$test1Tag->id, $test3Tag->id]);

    $this
        ->post(
            action([SubscribeController::class, 'store'], test()->emailList->uuid),
            payloadWithRedirects([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'tags' => 'test1;test2;test3',
            ])
        );

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::findForEmail(payloadWithRedirects()['email'], test()->emailList);

    expect($subscriber->tags()->pluck('name')->toArray())->toEqual(['test1', 'test3']);
});

it('will redirect to the correct url if the email address is already subscribed', function () {
    test()->emailList->subscribe(payloadWithRedirects()['email']);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects())
        ->assertRedirect(payloadWithRedirects()['redirect_after_already_subscribed']);
});

it('will add tags if the email address is already subscribed', function () {
    $tag1 = Tag::create(['name' => 'test1', 'email_list_id' => test()->emailList->id]);
    $tag2 = Tag::create(['name' => 'test2', 'email_list_id' => test()->emailList->id]);
    $tag3 = Tag::create(['name' => 'test3', 'email_list_id' => test()->emailList->id]);

    test()->emailList->allowedFormSubscriptionTags()->sync([$tag1->id, $tag2->id, $tag3->id]);

    test()->emailList->subscribe(payloadWithRedirects()['email']);
    $subscriber = Subscriber::findForEmail(payloadWithRedirects()['email'], test()->emailList);
    $subscriber->addTags(['test1', 'test2']);

    expect($subscriber->fresh()->tags()->count())->toEqual(2);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects([
            'tags' => 'test3',
        ]))
        ->assertRedirect(payloadWithRedirects()['redirect_after_already_subscribed']);

    expect($subscriber->tags()->count())->toEqual(3);
});

test('when not specified on the form it will redirect to the redirect after already subscribed url on the list', function () {
    test()->withoutExceptionHandling();

    test()->emailList->subscribe(payload()['email']);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertRedirect(test()->emailList->redirect_after_already_subscribed);
});

test('when no redirect after already subscribed is specified on the request or email list it will redirect show a view', function () {
    test()->emailList->subscribe(payload()['email']);

    test()->emailList->update(['redirect_after_already_subscribed' => null]);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.alreadySubscribed');
});

it('will redirect to the correct url if the subscription is pending', function () {
    test()->emailList->update(['requires_confirmation' => true]);

    test()->emailList->subscribe(payloadWithRedirects()['email']);

    $redirectUrl = 'https://mydomain/subscription-pending';

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payloadWithRedirects(
            ['redirect_after_subscription_pending' => $redirectUrl]
        ))
        ->assertRedirect($redirectUrl);
});

test('when not specified on the form it will redirect to the redirect after subscription pending url on the list', function () {
    test()->emailList->update(['requires_confirmation' => true]);
    test()->emailList->subscribe(payload()['email']);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertRedirect(test()->emailList->redirect_after_subscription_pending);
});

test('when no redirect after subscription pending is specified on the request or email list it will redirect show a view', function () {
    test()->withoutExceptionHandling();

    test()->emailList->update(['requires_confirmation' => true]);
    test()->emailList->subscribe(payload()['email']);

    test()->emailList->update(['redirect_after_subscription_pending' => null]);

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), payload())
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.confirmSubscription');
});

test('clicking the link in the confirm subscription mail will redirect to the given url', function () {
    test()->emailList->update(['requires_confirmation' => true, 'redirect_after_subscription_pending' => null]);

    $payload = payloadWithRedirects();
    $payload['redirect_after_subscription_pending'] = null;

    /*
     * We'll grab the url behind the confirm subscription button in the mail that will be sent
     */
    Event::listen(MessageSent::class, function (MessageSent $event) {
        test()->confirmSubscriptionLink = (new Crawler($event->message->getHtmlBody()))
            ->filter('.button-primary')->first()->attr('href');
    });

    $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList->uuid), $payload);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::findForEmail($payload['email'], test()->emailList);
    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Unconfirmed);

    /*
     * We'll pretend the user clicked the confirm subscription button by visiting the url
     */
    $this
        ->get(test()->confirmSubscriptionLink)
        ->assertRedirect($payload['redirect_after_subscribed']);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Subscribed);
});

it('will render turnstile when required', function () {
    config()->set('mailcoach.turnstile_secret', 'some-secret');

    $response = $this
        ->post(action([SubscribeController::class, 'store'], test()->emailList), payloadWithRedirects());

    $response->assertSee('challenges.cloudflare.com');
});

it('wont render turnstile when it\'s a json request', function () {
    config()->set('mailcoach.turnstile_secret', 'some-secret');

    $this
        ->postJson(action([SubscribeController::class, 'store'], test()->emailList), payloadWithRedirects())
        ->assertRedirect(payloadWithRedirects()['redirect_after_subscribed']);

    test()->assertEquals(
        SubscriptionStatus::Subscribed,
        test()->emailList->getSubscriptionStatus(test()->email)
    );
});

// Helpers
function payload(array $extraAttributes = [])
{
    return array_merge([
        'email' => test()->email,
    ], $extraAttributes);
}

function payloadWithRedirects(array $extraAttributes = []): array
{
    return array_merge([
        'redirect_after_subscribed' => 'https://mydomain/subscribed',
        'redirect_after_already_subscribed' => 'https://mydomain/already-subscribed',
        'redirect_after_subscription_pending' => 'https://mydomain/subscription-pending',
    ], test()->payload($extraAttributes));
}
