<?php

namespace Spatie\Mailcoach\Tests\Features\TransactionalMails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

class StoreTransactionalMailTest extends TestCase
{
    /** @test */
    public function a_transactional_mail_will_be_stored_in_the_db()
    {
        Mail::to('john@example.com')->send(new TestTransactionMail());

        $this->assertCount(1, TransactionalMail::get());
    }
}
