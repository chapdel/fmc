<?php


namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class TestTransactionalMailReplacer implements TransactionalMailReplacer
{
    public function helpText(): array
    {
        return [
            'test' => 'A test replacer',
        ];
    }

    public function replace(string $templateText, Mailable $mailable): string
    {
        if (! $mailable instanceof TestMailableWithTemplate) {
            return $templateText;
        }

        return str_replace('::argument::', $mailable->argument, $templateText);
    }
}
