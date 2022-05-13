<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailPerformance extends Component
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
        return view('mailcoach::app.transactionalMails.performance')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __('mailcoach - Performance'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
