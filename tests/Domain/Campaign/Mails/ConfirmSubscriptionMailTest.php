<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestClasses\CustomConfirmSubscriberMail;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
        'name' => 'my newsletter',
        'transactional_mailer' => 'some-transactional-mailer',
    ]);
});

test('the confirmation mail is sent with the correct mailer', function () {
    Mail::fake();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
        expect($mail->mailer)->toEqual('some-transactional-mailer');

        return true;
    });
});

test('the confirmation mail has a default subject', function () {
    Mail::fake();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
        $mail->build();

        expect($mail->subject)->toContain('Confirm');

        return true;
    });
});

test('the subject of the confirmation mail can be customized', function () {
    Mail::fake();

    $template = TransactionalMailTemplate::factory()->create([
        'subject' => 'Hello ::subscriber.first_name::, welcome to ::list.name::',
    ]);

    test()->emailList->update(['confirmation_mail_id' => $template->id]);

    Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])
        ->subscribeTo(test()->emailList);

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
        $mail->build();
        expect($mail->subject)->toEqual('Hello John, welcome to my newsletter');

        return true;
    });
});

test('the confirmation mail has default content', function () {
    test()->emailList->update(['transactional_mailer' => 'log']);

    $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    $content = (new ConfirmSubscriberMail($subscriber))->render();

    expect($content)->toContain('confirm');
});

test('the confirmation mail can have custom content', function () {
    test()->emailList->update(['transactional_mailer' => 'log']);

    Subscriber::$fakeUuid = 'my-uuid';

    $template = TransactionalMailTemplate::factory()->create([
        'body' => 'Hi ::subscriber.first_name::, press ::confirmUrl:: to subscribe to ::list.name::',
    ]);

    test()->emailList->update(['confirmation_mail_id' => $template->id]);

    $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    $content = (new ConfirmSubscriberMail($subscriber))->render();

    expect($content)->toContain('Hi John, press http://localhost/mailcoach/confirm-subscription/my-uuid to subscribe to my newsletter');
});

it('can use custom welcome mailable', function () {
    Mail::fake();

    test()->emailList->update(['confirmation_mailable_class' => CustomConfirmSubscriberMail::class]);

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(CustomConfirmSubscriberMail::class);
});
