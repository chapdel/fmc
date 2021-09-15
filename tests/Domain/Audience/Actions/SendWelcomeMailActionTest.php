<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendWelcomeMailAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Tests\TestCase;



beforeEach(function () {
    parent::setup();

    test()->subscriber = Subscriber::factory()->create();

    test()->subscriber->emailList->update([
        'send_welcome_mail' => true,
        'transactional_mailer' => 'some-mailer',
    ]);
});

it('can send a welcome mail with the correct mailer', function () {
    Mail::fake();

    $action = new SendWelcomeMailAction();

    $action->execute(test()->subscriber);

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
        expect($mail->mailer)->toEqual('some-mailer');

        return true;
    });
});
