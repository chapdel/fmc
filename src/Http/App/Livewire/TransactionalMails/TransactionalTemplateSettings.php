<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class TransactionalTemplateSettings extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TransactionalMailTemplate $template;

    protected function rules(): array
    {
        return [
            'template.name' => 'required',
            'template.type' => 'required',
            'template.store_mail' => '',
        ];
    }

    public function mount(TransactionalMailTemplate $transactionalMailTemplate)
    {
        $this->authorize('update', $transactionalMailTemplate);

        $this->template = $transactionalMailTemplate;
    }

    public function save()
    {
        $this->validate();

        $this->template->save();

        $this->flash(__('mailcoach - Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.settings')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __('mailcoach - Settings'),
                'template' => $this->template,
            ]);
    }
}
