<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Mailgun\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\EventType;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Exceptions\CouldNotAccessAccountSetting;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Mailgun;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;
use Spatie\MailcoachMailgunFeedback\MailgunWebhookController;

class FeedbackStepComponent extends StepComponent
{
    use UsesMailer;

    public bool $trackOpens = false;

    public bool $trackClicks = false;

    public string $signingSecret = '';

    public array $rules = [
        'trackOpens' => ['boolean'],
        'trackClicks' => ['boolean'],
        'signingSecret' => ['required'],
    ];

    public function mount()
    {
        $this->trackOpens = $this->mailer()->get('open_tracking_enabled', '');
        $this->trackClicks = $this->mailer()->get('click_tracking_enabled', '');
        $this->signingSecret = $this->mailer()->get('signing_secret', '');
    }

    public function configureMailgun()
    {
        $this->validate();

        $endpoint = action(MailgunWebhookController::class, $this->mailer()->configName());

        $events = [EventType::PermanentFail, EventType::Complained];

        if ($this->trackOpens) {
            $events[] = EventType::Opened;
        }

        if ($this->trackClicks) {
            $events[] = EventType::Clicked;
        }

        try {
            $this->getMailgun()->setupWebhook($endpoint, $events);
        } catch (CouldNotAccessAccountSetting $exception) {
            notifyError($exception->getMessage());

            return;
        }

        $this->mailer()->merge([
            'open_tracking_enabled' => $this->trackOpens,
            'click_tracking_enabled' => $this->trackClicks,
            'signing_secret' => $this->signingSecret,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.mailgun.feedback');
    }

    protected function getMailgun(): Mailgun
    {
        return new Mailgun($this->mailer()->get('apiKey'), $this->mailer()->get('domain'), $this->mailer()->get('baseUrl'));
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
