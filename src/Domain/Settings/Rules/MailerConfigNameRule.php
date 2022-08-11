<?php

namespace Spatie\Mailcoach\Domain\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

class MailerConfigNameRule implements Rule
{
    public function passes($attribute, $value)
    {
        $mailerConfigNames = Mailer::all()->map(fn(Mailer $mailer) => $mailer->configName());

        return in_array($value, $mailerConfigNames);
    }

    public function message()
    {
        $mailerConfigNames = Mailer::all()
            ->map(fn(Mailer $mailer) => "`{$mailer->configName()}`")
            ->join(', ', ' and ');


        return "You must pass a valid mailer key. Valid keys are: {$mailerConfigNames}.";
    }
}
