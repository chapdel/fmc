<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\ValidationRules\Rules\Delimited;

class TransactionalTemplateContent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TransactionalMailTemplate $template;

    protected function rules(): array
    {
        return [
            'template.name' => '',
            'template.type' => '',
            'template.subject' => '',
            'template.to' => new Delimited('email'),
            'template.cc' => new Delimited('email'),
            'template.bcc' => new Delimited('email'),
            'template.body' => '',
            'template.structured_html' => '',
        ];
    }

    public function mount(TransactionalMailTemplate $template)
    {
        $this->authorize('update', $template);

        $this->template = $template;
    }

    public function save()
    {
        $this->validate();

        $this->template->update([
            'name' => $this->template->name,
            'type' => $this->template->type,
            'subject' => $this->template->subject,
            'body' => $this->template->body,
            'structured_html' => $this->template->structured_html,
            'to' => $this->delimitedToArray($this->template->to),
            'cc' => $this->delimitedToArray($this->template->cc),
            'bcc' => $this->delimitedToArray($this->template->bcc),
        ]);

        $this->flash(__('mailcoach - Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        $this->template->to = $this->template->toString();
        $this->template->cc = $this->template->ccString();
        $this->template->bcc = $this->template->bccString();

        return view('mailcoach::app.transactionalMails.templates.edit')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __('mailcoach - Details'),
                'template' => $this->template,
            ]);
    }

    protected function delimitedToArray(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }
}
