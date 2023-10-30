<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\MainNavigation;

class TransactionalTemplateSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    public ?string $name = null;

    public ?string $type = null;

    public bool $store_mail = false;

    protected function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'store_mail' => '',
        ];
    }

    public function mount(TransactionalMail $transactionalMailTemplate)
    {
        $this->authorize('update', $transactionalMailTemplate);

        $this->template = $transactionalMailTemplate;
        $this->fill($this->template->toArray());

        app(MainNavigation::class)->activeSection()?->add($this->template->name, route('mailcoach.transactionalMails.templates'));
    }

    public function save()
    {
        $this->validate();

        $this->template->name = $this->name;
        $this->template->type = $this->type;
        $this->template->store_mail = $this->store_mail;
        $this->template->save();

        notify(__mc('Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.settings')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __mc('Settings'),
                'template' => $this->template,
            ]);
    }
}
