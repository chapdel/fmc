<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Mail\Mailable;

class TestTransactionMail extends Mailable
{
    public function build()
    {
        return $this->text('test');
    }
}
