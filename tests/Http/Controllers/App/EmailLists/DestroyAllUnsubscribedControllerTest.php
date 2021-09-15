<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;


it('will delete all unsubscribers', function () {
    test()->authenticate();

    $emailList = EmailList::factory()->create(['requires_confirmation' => false]);
    $anotherEmailList = EmailList::factory()->create(['requires_confirmation' => false]);

    $subscriber = Subscriber::createWithEmail('subscribed@example.com')->subscribeTo($emailList);

    $unsubscribedSubscriber = Subscriber::createWithEmail('unsubscribed@example.com')
        ->subscribeTo($emailList)
        ->unsubscribe();

    $unsubscribedSubscriberOfAnotherList = Subscriber::createWithEmail('unsubscribed-other-list@example.com')
        ->subscribeTo($anotherEmailList)
        ->unsubscribe();

    $this
        ->delete(route('mailcoach.emailLists.destroy-unsubscribes', $emailList->refresh()))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $existingSubscriberIds = Subscriber::pluck('id')->toArray();

    expect(in_array($subscriber->id, $existingSubscriberIds))->toBeTrue();
    expect(in_array($unsubscribedSubscriber->id, $existingSubscriberIds))->toBeFalse();
    expect(in_array($unsubscribedSubscriberOfAnotherList->id, $existingSubscriberIds))->toBeTrue();
});
