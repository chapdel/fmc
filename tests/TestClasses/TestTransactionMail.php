<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Mails\Concerns\StoreMail;

class
TestTransactionMail extends Mailable
{
    use StoreMail;

    public static ?\Closure $buildUsing = null;

    public string $name = 'John Doe';

    public function build()
    {
        $this->text('test');

        if (self::$buildUsing) {
            (self::$buildUsing)($this);
        }

        ray($this->trackOpens, $this->trackClicks);

        return $this;
    }
}
