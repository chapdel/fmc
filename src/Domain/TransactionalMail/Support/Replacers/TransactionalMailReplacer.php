<?php


namespace Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers;

use Illuminate\Mail\Mailable;

interface TransactionalMailReplacer
{
    public function helpText(): array;

    public function replace(string $text, Mailable $mailable): string;

}
