<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomWelcomeMail;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create([
        'name' => 'my newsletter',
        'requires_confirmation' => false,
        'send_welcome_mail' => true,
        'transactional_mailer' => 'some-transactional-mailer',
    ]);
});

it('will send a welcome mail when a subscriber has subscribed with the correct mailer', function () {
    Mail::fake();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
        expect($mail->mailer)->toEqual('some-transactional-mailer');

        return true;
    });
});

it('will not send a welcome mail if it is not enabled on the email list', function () {
    Mail::fake();

    test()->emailList->update(['send_welcome_mail' => false]);

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertNothingQueued();
});

it('will send a welcome mail when a subscribed gets confirmed', function () {
    Mail::fake();

    test()->emailList->update(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertNotQueued(WelcomeMail::class);

    $subscriber->confirm();

    Mail::assertQueued(WelcomeMail::class);
});

test('the welcome mail has a default subject', function () {
    Mail::fake();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
        $mail->build();

        expect($mail->subject)->toContain('Welcome');

        return true;
    });
});

test('the subject of the welcome mail can be customized', function () {
    Mail::fake();

    test()->emailList->update(['welcome_mail_subject' => 'Hello ::subscriber.first_name::, welcome to ::list.name::']);

    Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
        $mail->build();
        expect($mail->subject)->toEqual('Hello John, welcome to my newsletter');

        return true;
    });
});

test('the welcome mail has default content', function () {
    test()->emailList->update(['transactional_mailer' => 'log']);

    $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    $content = (new WelcomeMail($subscriber))->render();

    expect($content)->toContain('You are now subscribed');
});

test('the welcome mail can have custom content', function () {
    test()->emailList->update(['transactional_mailer' => 'log']);

    Subscriber::$fakeUuid = 'my-uuid';

    test()->emailList->update(['welcome_mail_content' => 'Hi ::subscriber.first_name::, welcome to ::list.name::. Here is a link to unsubscribe ::unsubscribeUrl::']);

    $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    $content = (new WelcomeMail($subscriber))->render();

    expect($content)->toContain('Hi John, welcome to my newsletter. Here is a link to unsubscribe http://localhost/mailcoach/unsubscribe/my-uuid');
});

it('can use custom welcome mailable', function () {
    Mail::fake();

    test()->emailList->update(['welcome_mailable_class' => CustomWelcomeMail::class]);

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    Mail::assertQueued(CustomWelcomeMail::class);
});
