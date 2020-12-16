<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Closure;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Mails\Concerns\StoresMail;

class
TestTransactionMail extends Mailable
{
    use StoresMail;

    public static ?Closure $buildUsing = null;

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
