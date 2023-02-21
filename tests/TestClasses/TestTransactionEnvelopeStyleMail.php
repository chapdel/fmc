<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Closure;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;

class TestTransactionEnvelopeStyleMail extends Mailable
{
    use StoresMail;

    public string $name = 'John Doe';

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test mail envelope style');
    }

    public function content()
    {
        $this->store();

        return new Content(text: 'test');
    }
}
