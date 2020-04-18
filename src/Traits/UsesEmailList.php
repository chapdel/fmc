<?php

namespace Spatie\Mailcoach\Traits;

trait UsesEmailList
{
    private string $emailListClass;

    public function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list');
    }
}
