<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Sendinblue\Steps;

use Spatie\Mailcoach\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 1;

    public int $mailsPerTimeSpan = 100;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.sendinblue.throttling');
    }
}
