<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;


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

    test()->post(route('mailcoach.subscriber.resend-confirmation-mail', $subscriber));
    Mail::assertQueued(ConfirmSubscriberMail::class, 2);
});
