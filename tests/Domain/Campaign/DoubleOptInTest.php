<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Front\Controllers\ConfirmSubscriberController;
use Symfony\Component\DomCrawler\Crawler;

beforeEach(function () {
    /* @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    test()->emailList = EmailList::factory()->create(['requires_confirmation' => true]);

    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getBody()))
            ->filter('.button-primary')->first()->attr('href');

        test()->mailedLink = Str::after($link, 'http://localhost');
    });

    test()->emailList->subscribe('john@example.com');
});

test('when subscribing to a double opt in list a click in the confirmation mail is needed to subscribe', function () {
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeFalse();

    $this
        ->get(test()->mailedLink)
        ->assertSuccessful();

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
});

test('clicking the mailed link twice will not result in a double subscription', function () {
    $this
        ->get(test()->mailedLink)
        ->assertSuccessful();

    $this
        ->get(test()->mailedLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.alreadySubscribed');

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
    expect(Subscriber::count())->toEqual(1);
});

test('clicking on an invalid link will render to correct response', function () {
    $content = $this
        ->get(action(ConfirmSubscriberController::class, 'invalid-uuid'))
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.couldNotFindSubscription');
});
