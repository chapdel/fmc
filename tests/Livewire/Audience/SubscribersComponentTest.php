<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;

beforeEach(function () {
    $this->emailList = EmailList::factory()->create();
    test()->authenticate();
});

it('can resend the confirmation mail with the correct mailer', function () {
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

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('resendConfirmation', $subscriber);

    Mail::assertQueued(ConfirmSubscriberMail::class, 2);
});

it('can confirm a subscriber', function () {
    $emailList = EmailList::factory()->create(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::Unconfirmed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('confirm', $subscriber);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Subscribed);
});

it('will confirm unconfirmed subscribers', function () {
    $subscriber = Subscriber::factory()->create([
        'unsubscribed_at' => now(),
        'subscribed_at' => now(),
    ]);

    expect($subscriber->status)->toEqual(SubscriptionStatus::Unsubscribed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('confirm', $subscriber)
        ->assertSessionHas('filament.notifications');
});

it('can resubscribe a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::Unsubscribed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('resubscribe', $subscriber);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Subscribed);
});

it('will only resubscribe unsubscribed subscribers', function () {
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::Subscribed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('resubscribe', $subscriber)
        ->assertSessionHas('filament.notifications');
});

it('can unsubscribe a subscriber', function () {
    $emailList = EmailList::factory()->create();

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::Subscribed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('unsubscribe', $subscriber);

    expect($subscriber->refresh()->status)->toEqual(SubscriptionStatus::Unsubscribed);
});

it('will only unsubscribe subscribed subscribers', function () {
    $emailList = EmailList::factory()->create();
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
    $subscriber->unsubscribe();

    expect($subscriber->status)->toEqual(SubscriptionStatus::Unsubscribed);

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('unsubscribe', $subscriber)
        ->assertSessionHas('filament.notifications');
});

it('can delete a subscriber', function () {
    test()->authenticate();

    $subscriber = Subscriber::factory()->create();

    Livewire::test(SubscribersComponent::class, ['emailList' => $this->emailList])
        ->call('deleteSubscriber', $subscriber);

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

    \Livewire\Livewire::test(SubscribersComponent::class, ['emailList' => $emailList])
        ->call('deleteUnsubscribes');

    $existingSubscriberIds = Subscriber::pluck('id')->toArray();

    expect(in_array($subscriber->id, $existingSubscriberIds))->toBeTrue();
    expect(in_array($unsubscribedSubscriber->id, $existingSubscriberIds))->toBeFalse();
    expect(in_array($unsubscribedSubscriberOfAnotherList->id, $existingSubscriberIds))->toBeTrue();
});
