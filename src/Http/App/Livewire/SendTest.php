<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\ValidationRules\Rules\Delimited;

class SendTest extends Component
{
    use LivewireFlash;

    public Model $model;

    public string $emails = '';

    public string $html = '';

    public function mount(Model $model)
    {
        $this->model = $model;
        $this->emails = Auth::user()->email;
    }

    public function sendTest()
    {
        $this->validate([
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ], [
            'email.required' => __('mailcoach - You must specify at least one e-mail address.'),
            'email.email' => __('mailcoach - Not all the given e-mails are valid.'),
        ]);

        $emails = array_map('trim', explode(',', $this->emails));

        if ($this->model instanceof Sendable) {
            try {
                $this->model->sendTestMail($emails);
            } catch (\Throwable $e) {
                $this->flashError($e->getMessage());
                $this->dispatchBrowserEvent('modal-closed', ['modal' => 'send-test']);

                return;
            }

            if (count($emails) > 1) {
                $this->flash(__('mailcoach - A test email was sent to :count addresses.', ['count' => count($emails)]));
            } else {
                $this->flash(__('mailcoach - A test email was sent to :email.', ['email' => $emails[0]]));
            }
        } else {
            $this->flashError(__('mailcoach - Model :model does not support sending tests.', ['model' => $this->model::class]));
        }

        $this->dispatchBrowserEvent('modal-closed', ['modal' => 'send-test']);
    }

    public function render()
    {
        return view('mailcoach::app.components.sendTest');
    }
}
