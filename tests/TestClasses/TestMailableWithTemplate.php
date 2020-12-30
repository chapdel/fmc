<?php


namespace Spatie\Mailcoach\Tests\TestClasses;


use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;

class TestMailableWithTemplate extends Mailable
{
    use UsesMailcoachTemplate;

    public string $argument;

    public function __construct(string $argument = 'test-argument')
    {
        $this->argument = $argument;

        $this->template('test-template');
    }

    public function build()
    {
        $this
            ->to('john@example.com')
            ->template('test-template');
    }

    public static function testInstance(): self
    {
        return new self('test-argument');
    }

    // {{ $user->email }}   [email]
}
