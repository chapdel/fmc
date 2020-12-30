<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class InvalidTemplate extends Exception
{
    public static function mailableClassNotFound(TransactionalMailTemplate $template): self
    {
        return new self("The class specified in `test_using_mailable` (`{$template->test_using_mailable}`) could not be found. Make sure the `test_using_mailable` exists.");
    }

    public static function mailableClassNotValid(TransactionalMailTemplate $template): self
    {
        $expectedTrait = UsesMailcoachTemplate::class;

        return new self("The class specified in `test_using_mailable` (`{$template->test_using_mailable}`) is invalid. Make sure your mailable uses the `{$expectedTrait}` trait.");
    }
}
