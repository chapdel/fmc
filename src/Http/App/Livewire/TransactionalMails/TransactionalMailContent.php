<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailContent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public TransactionalMail $transactionalMail;

    public function mount(TransactionalMail $transactionalMail)
    {
        $this->transactionalMail = $transactionalMail;
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.content')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __('mailcoach - Content'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
