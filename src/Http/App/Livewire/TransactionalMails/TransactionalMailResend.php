<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class TransactionalMailResend extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;
    use LivewireFlash;

    public TransactionalMail $transactionalMail;

    public function mount(TransactionalMail $transactionalMail)
    {
        $this->transactionalMail = $transactionalMail;
    }

    public function resend()
    {
        $this->transactionalMail->resend();

        $this->flash(__('mailcoach - The mail has been resent!'));
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.resend')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __('mailcoach - Resend'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
