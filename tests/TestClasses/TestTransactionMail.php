<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Mails\Concerns\StoreMail;

class TestTransactionMail extends Mailable
{
    use StoreMail;

    public function build()
    {
        return $this
            ->text('test')
            ->trackOpensAndClicks();
    }
}
