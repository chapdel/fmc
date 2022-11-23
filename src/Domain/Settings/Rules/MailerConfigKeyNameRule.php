<?php

namespace Spatie\Mailcoach\Domain\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class MailerConfigKeyNameRule implements Rule
{
    use UsesMailcoachModels;

    public function passes($attribute, $value)
    {
        return self::getMailerClass()::where('config_key_name', $value)->exists();
    }

    public function message()
    {
        $mailerConfigNames = self::getMailerClass()::all()
            ->map(fn (Mailer $mailer) => "`{$mailer->config_key_name}`")
            ->join(', ', ' and ');

        return "You must pass a valid mailer key. Valid values are: {$mailerConfigNames}.";
    }
}
