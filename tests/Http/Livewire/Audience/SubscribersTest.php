<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers;

beforeEach(function () {
    $this->emailList = EmailList::factory()->create();
});

it('can resend the confirmation mail with the correct mailer', function () {
    test()->authenticate();
    Mail::fake();

    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
        'transactional_mailer' => 'some-mailer',
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
        expect($mail->mailer)->toEqual('some-mailer');

        return true;
    });

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('resendConfirmation', $subscriber->id);

    Mail::assertQueued(ConfirmSubscriberMail::class, 2);
});

it('can confirm a subscriber', function () {
    $emailList = EmailList::factory()->create(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNCONFIRMED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('confirm', $subscriber->id);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
});

it('will confirm unconfirmed subscribers', function () {
    $subscriber = Subscriber::factory()->create([
        'unsubscribed_at' => now(),
        'subscribed_at' => now(),
    ]);

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('confirm', $subscriber->id)
        ->assertDispatchedBrowserEvent('notify', [
            'content' => __('mailcoach - Can only subscribe unconfirmed emails'),
            'type' => 'error',
        ]);
});

it('can resubscribe a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('resubscribe', $subscriber->id);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
});

it('will only resubscribe unsubscribed subscribers', function () {
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('resubscribe', $subscriber->id)
        ->assertDispatchedBrowserEvent('notify', [
            'content' => __('mailcoach - Can only resubscribe unsubscribed subscribers'),
            'type' => 'error',
        ]);
});

it('can unsubscribe a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('unsubscribe', $subscriber->id);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

it('will only unsubscribe subscribed subscribers', function () {
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('unsubscribe', $subscriber->id)
        ->assertDispatchedBrowserEvent('notify', [
            'content' => __('mailcoach - Can only unsubscribe a subscribed subscriber'),
            'type' => 'error',
        ]);
});

it('can delete a subscriber', function () {
    test()->authenticate();

    $subscriber = Subscriber::factory()->create();

    Livewire::test(Subscribers::class, ['emailList' => $this->emailList])
        ->call('deleteSubscriber', $subscriber->id);

    expect(Subscriber::count())->toBe(0);
});

it('can delete all unsubscribers', function () {
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

    \Livewire\Livewire::test(Subscribers::class, ['emailList' => $emailList])
        ->call('deleteUnsubscribes');

    $existingSubscriberIds = Subscriber::pluck('id')->toArray();

    expect(in_array($subscriber->id, $existingSubscriberIds))->toBeTrue();
    expect(in_array($unsubscribedSubscriber->id, $existingSubscriberIds))->toBeFalse();
    expect(in_array($unsubscribedSubscriberOfAnotherList->id, $existingSubscriberIds))->toBeTrue();
});
