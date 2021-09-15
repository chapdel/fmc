<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Domain\Audience\Mails\EmailListSummaryMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
    test()->emailList = EmailList::factory()->create([
        'report_recipients' => 'john@example.com,jane@example.com',
        'report_email_list_summary' => true,
    ]);
});

it('can send the email list summary with the correct mailer', function () {
    Mail::fake();
    config()->set('mailcoach.mailer', 'some-mailer');

    test()->artisan(SendEmailListSummaryMailCommand::class);
    Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
        test()->assertEquals('2019-01-01 00:00:00', $mail->summaryStartDateTime->toDateTimeString());
        test()->assertEquals('some-mailer', $mail->mailer);

        return true;
    });
    test()->assertEquals('2019-01-01 00:00:00', test()->emailList->refresh()->email_list_summary_sent_at->format('Y-m-d H:i:s'));
});

it('can send the email list summary with the default mailer', function () {
    Mail::fake();
    config()->set('mail.default', 'some-mailer');

    test()->artisan(SendEmailListSummaryMailCommand::class);
    Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
        test()->assertEquals('some-mailer', $mail->mailer);

        return true;
    });
});

it('will not send the email list summary mail if it is not enabled', function () {
    Mail::fake();

    test()->emailList->update(['report_email_list_summary' => false]);

    test()->artisan(SendEmailListSummaryMailCommand::class);
    Mail::assertNotQueued(EmailListSummaryMail::class);
});

it('will not send an email list summary twice on one day', function () {
    Mail::fake();

    test()->emailList->update([
        'email_list_summary_sent_at' => now(),
    ]);

    test()->artisan(SendEmailListSummaryMailCommand::class);

    Mail::assertNotQueued(EmailListSummaryMail::class);
});

it('will send the email list summary starting from the previous sent date', function () {
    Mail::fake();

    TestTime::addWeek();

    test()->emailList->update([
        'email_list_summary_sent_at' => now(),
    ]);

    TestTime::addWeek();

    test()->artisan(SendEmailListSummaryMailCommand::class);
    Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
        test()->assertEquals('2019-01-08 00:00:00', $mail->summaryStartDateTime->toDateTimeString());

        return true;
    });
});

test('the content of the email list summary mail is valid', function () {
    test()->assertIsString((new EmailListSummaryMail(test()->emailList, now()))->render());
});
