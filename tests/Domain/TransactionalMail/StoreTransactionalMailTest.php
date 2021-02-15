<?php

namespace Spatie\Mailcoach\Tests\Domain\TransactionalMail;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailOpenedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

class StoreTransactionalMailTest extends TestCase
{
    use SendsTestTransactionalMail;

    /** @test */
    public function a_transactional_mail_will_be_stored_in_the_db()
    {
        $this->sendTestMail(function (TestTransactionMail $mail) {
            $mail
                ->subject('This is the subject')
                ->trackOpensAndClicks();
        });

        $this->assertCount(1, TransactionalMail::get());
        $this->assertCount(1, Send::get());

        $transactionalMail = TransactionalMail::first();

        $this->assertEquals(
            [['email' => config('mail.from.address'), 'name' => config('mail.from.name')]],
            $transactionalMail->from,
        );
        $this->assertEquals('This is the subject', $transactionalMail->subject);
        $this->assertStringContainsString('This is the content for John Doe', $transactionalMail->body);
        $this->assertTrue($transactionalMail->track_opens);
        $this->assertTrue($transactionalMail->track_clicks);
        $this->assertEquals(TestTransactionMail::class, $transactionalMail->mailable_class);
        $this->assertInstanceOf(Send::class, $transactionalMail->send);
        $this->assertInstanceOf(TransactionalMail::class, Send::first()->transactionalMail);
    }

    /** @test */
    public function it_can_store_the_various_recipients()
    {
        $this->sendTestMail(function (TestTransactionMail $testTransactionMail) {
            $testTransactionMail
                ->trackOpensAndClicks()
                ->from('ringo@example.com', 'Ringo')
                ->to('john@example.com', 'John')
                ->cc('paul@example.com', 'Paul')
                ->bcc('george@example.com', 'George');
        });

        $transactionalMail = TransactionalMail::first();

        $this->assertEquals(
            [['email' => 'ringo@example.com', 'name' => 'Ringo']],
            $transactionalMail->from,
        );

        $this->assertEquals(
            [['email' => 'john@example.com', 'name' => 'John']],
            $transactionalMail->to,
        );

        $this->assertEquals(
            [['email' => 'paul@example.com', 'name' => 'Paul']],
            $transactionalMail->cc,
        );

        $this->assertEquals(
            [['email' => 'george@example.com', 'name' => 'George']],
            $transactionalMail->bcc,
        );
    }

    /** @test */
    public function only_opens_on_transactional_mails_can_be_tracked()
    {
        $this->sendTestMail(function (TestTransactionMail $mail) {
            $mail->trackOpens();
        });

        $transactionalMail = TransactionalMail::first();
        $this->assertTrue($transactionalMail->track_opens);
        $this->assertFalse($transactionalMail->track_clicks);
    }

    /** @test */
    public function only_click_on_transactional_mails_can_be_tracked()
    {
        $this->sendTestMail(function (TestTransactionMail $mail) {
            $mail->store();
        });

        $transactionalMail = TransactionalMail::first();
        $this->assertFalse($transactionalMail->track_opens);
        $this->assertFalse($transactionalMail->track_clicks);
    }

    /** @test */
    public function by_default_it_will_not_store_any_mails()
    {
        $this->sendTestMail(function (TestTransactionMail $mail) {
        });

        $this->assertCount(0, TransactionalMail::get());
    }

    /** @test */
    public function a_send_for_a_transactional_mail_can_be_marked_as_opened()
    {
        Event::fake([TransactionalMailOpenedEvent::class]);

        $this->sendTestMail();

        /** @var Send $send */
        $send = Send::first();

        $send->registerOpen();

        $this->assertCount(1, $send->transactionalMailOpens);

        Event::assertDispatched(TransactionalMailOpenedEvent::class);
    }

    /** @test */
    public function a_send_for_a_transactional_mail_can_be_marked_as_clicked()
    {
        Event::fake([TransactionalMailLinkClickedEvent::class]);

        $this->sendTestMail();

        /** @var Send $send */
        $send = Send::first();

        $send->registerClick('https://spatie.be');

        $this->assertCount(1, $send->transactionalMailClicks);

        Event::assertDispatched(TransactionalMailLinkClickedEvent::class);
    }
}
