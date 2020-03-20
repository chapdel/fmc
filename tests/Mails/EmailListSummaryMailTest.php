<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Mails\EmailListSummaryMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class EmailListSummaryMailTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

        parent::setUp();

        $this->emailList = factory(EmailList::class)->create([
            'report_recipients' => 'john@example.com,jane@example.com',
            'report_email_list_summary' => true,
        ]);
    }

    /** @test */
    public function it_can_send_the_email_list_summary_with_the_correct_mailer()
    {
        Mail::fake();
        config()->set('mailcoach.mailer', 'some-mailer');

        $this->artisan(SendEmailListSummaryMailCommand::class);
        Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
            $this->assertEquals('2019-01-01 00:00:00', $mail->summaryStartDateTime->toDateTimeString());
            $this->assertEquals('some-mailer', $mail->mailer);

            return true;
        });
        $this->assertEquals('2019-01-01 00:00:00', $this->emailList->refresh()->email_list_summary_sent_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_send_the_email_list_summary_with_the_default_mailer()
    {
        Mail::fake();
        config()->set('mail.default', 'some-mailer');

        $this->artisan(SendEmailListSummaryMailCommand::class);
        Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
            $this->assertEquals('some-mailer', $mail->mailer);

            return true;
        });
    }

    /** @test */
    public function it_will_not_send_the_email_list_summary_mail_if_it_is_not_enabled()
    {
        Mail::fake();

        $this->emailList->update(['report_email_list_summary' => false]);

        $this->artisan(SendEmailListSummaryMailCommand::class);
        Mail::assertNotQueued(EmailListSummaryMail::class);
    }

    /** @test */
    public function it_will_not_send_an_email_list_summary_twice_on_one_day()
    {
        Mail::fake();

        $this->emailList->update([
            'email_list_summary_sent_at' => now(),
        ]);

        $this->artisan(SendEmailListSummaryMailCommand::class);

        Mail::assertNotQueued(EmailListSummaryMail::class);
    }

    /** @test */
    public function it_will_send_the_email_list_summary_starting_from_the_previous_sent_date()
    {
        Mail::fake();

        TestTime::addWeek();

        $this->emailList->update([
            'email_list_summary_sent_at' => now(),
        ]);

        TestTime::addWeek();

        $this->artisan(SendEmailListSummaryMailCommand::class);
        Mail::assertQueued(EmailListSummaryMail::class, function (EmailListSummaryMail $mail) {
            $this->assertEquals('2019-01-08 00:00:00', $mail->summaryStartDateTime->toDateTimeString());

            return true;
        });
    }

    /** @test */
    public function the_content_of_the_email_list_summary_mail_is_valid()
    {
        $this->assertIsString((new EmailListSummaryMail($this->emailList, now()))->render());
    }
}
