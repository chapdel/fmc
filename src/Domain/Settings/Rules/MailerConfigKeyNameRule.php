<?php

namespace Spatie\Mailcoach\Domain\Settings\Rules;

use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

class MailerConfigKeyNameRule
{
    public function passes($attribute, $value)
    {
        return Mailer::firstWhere('config_key_name', $value)->exists();
    }

    public function message()
    {
        $mailerConfigNames = Mailer::all()
            ->map(fn (Mailer $mailer) => "`{$mailer->config_key_name}`")
            ->join(', ', ' and ');


        return "You must pass a valid mailer key. Valid values are: {$mailerConfigNames}.";
    }
}
