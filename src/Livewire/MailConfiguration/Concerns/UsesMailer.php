<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Concerns;

use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\MailConfiguration\Smtp\Steps\SummaryStepComponent;

trait UsesMailer
{
    use UsesMailcoachModels;

    private ?Mailer $mailer = null;

    public function mailer(): Mailer
    {
        if ($this->mailer) {
            return $this->mailer;
        }

        $mailerId = $this->state()->forStep(SummaryStepComponent::class)['mailerId'];

        $this->mailer = self::getMailerClass()::find($mailerId);

        return $this->mailer;
    }
}
