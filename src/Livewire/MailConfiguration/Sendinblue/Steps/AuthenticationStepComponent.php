<?php

namespace Spatie\Mailcoach\Livewire\MailConfiguration\Sendinblue\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Sendinblue;
use Spatie\Mailcoach\Livewire\MailConfiguration\Concerns\UsesMailer;

class AuthenticationStepComponent extends StepComponent
{
    use UsesMailer;

    public string $apiKey = '';

    public $rules = [
        'apiKey' => ['required'],
    ];

    public function mount()
    {
        $this->apiKey = $this->mailer()->get('apiKey', '');
    }

    public function submit()
    {
        $this->validate();

        try {
            $validApiKey = (new Sendinblue($this->apiKey))->isValidApiKey();
        } catch (Exception) {
            notifyError(__mc('Something went wrong communicating with Sendinblue.'));

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', __mc('This is not a valid API key.'));

            return;
        }

        notify(__mc('The API key is correct.'));

        $this->mailer()->merge([
            'apiKey' => $this->apiKey,
        ]);

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Authenticate',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.sendinblue.authentication');
    }
}
