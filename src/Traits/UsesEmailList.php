<?php

namespace Spatie\Mailcoach\Traits;

use Spatie\Mailcoach\MailcoachRegistrar;

trait UsesEmailList
{
    private $emailListClass;

    public function getEmailListClass()
    {
        if (! isset($this->emailListClass)) {
            $this->emailListClass = app(MailcoachRegistrar::class)->getEmailListClass();
        }

        return $this->emailListClass;
    }
}
