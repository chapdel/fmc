<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Sendinblue\Steps;

use Exception;
use Illuminate\Support\Str;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Sendinblue;
use Spatie\Mailcoach\Http\Api\Controllers\Vendor\Sendinblue\SendinblueWebhookController;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class FeedbackStepComponent extends StepComponent
{
    use UsesMailer;

    public bool $trackOpens = true;

    public bool $trackClicks = true;

    public array $rules = [
        'trackOpens' => ['boolean'],
        'trackClicks' => ['boolean'],
    ];

    public function mount()
    {
        $this->trackOpens = $this->mailer()->get('open_tracking_enabled', true);
        $this->trackClicks = $this->mailer()->get('click_tracking_enabled', true);
    }

    public function configureSendinblue()
    {
        $this->validate();

        $endpoint = action(SendinblueWebhookController::class, $this->mailer()->configName());

        $secret = $this->mailer()->get('signing_secret', Str::random(20));

        $endpoint .= "?secret={$secret}";

        try {
            $this->getSendinblue()->setupWebhook($endpoint);
        } catch (Exception $e) {
            notifyError(__mc('Something went wrong while setting up the Sendinblue webhook'));
            report($e);

            return;
        }

        $this->mailer()->merge([
            'open_tracking_enabled' => $this->trackOpens,
            'click_tracking_enabled' => $this->trackClicks,
            'signing_secret' => $secret,
        ]);

        $this->mailer()->markAsReadyForUse();

        notify('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.sendinblue.feedback');
    }

    protected function getSendinblue(): Sendinblue
    {
        return new Sendinblue($this->mailer()->get('apiKey'));
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
