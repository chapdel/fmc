<?php

namespace Spatie\Mailcoach\Domain\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

class MailerConfigNameRule implements Rule
{
    public function passes($attribute, $value)
    {
        return Mailer::firstWhere('config_key_name', $value)->exists();
    }

    public function message()
    {
        $mailerConfigNames = Mailer::all()
            ->map(fn(Mailer $mailer) => "`{$mailer->config_key_name}`")
            ->join(', ', ' and ');


        return "You must pass a valid mailer key. Valid keys are: {$mailerConfigNames}.";
    }
}
