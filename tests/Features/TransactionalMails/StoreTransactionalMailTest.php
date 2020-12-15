<?php

namespace Spatie\Mailcoach\Tests\Features\TransactionalMails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

class StoreTransactionalMailTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::to('john@example.com')->send(new TestTransactionMail());
    }

    /** @test */
    public function a_transactional_mail_will_be_stored_in_the_db()
    {
        $this->assertCount(1, TransactionalMail::get());
        $this->assertCount(1, Send::get());
    }

    /** @test */
    public function a_send_for_a_transactional_mail_can_be_marked_as_opened()
    {
        /** @var Send $send */
        $send = Send::first();

        $send->registerOpen();
    }
}
