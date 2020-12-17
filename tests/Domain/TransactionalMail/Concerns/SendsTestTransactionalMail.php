<?php

namespace Spatie\Mailcoach\Tests\Features\TransactionalMails\Concerns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

trait SendsTestTransactionalMail
{
    protected function sendTestMail(callable $buildUsing = null): void
    {
        TestTransactionMail::$buildUsing = $buildUsing ?? function(TestTransactionMail $mail) {
                $mail->trackOpensAndClicks();
            };

        Mail::to('john@example.com')->send(new TestTransactionMail());
    }
}
