<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps;

use Spatie\Mailcoach\Http\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 60 * 60; // 1 hour

    public int $mailsPerTimeSpan = 100;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.mailgun.throttling');
    }
}
