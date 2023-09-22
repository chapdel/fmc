<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\MainNavigation;
use Spatie\ValidationRules\Rules\Delimited;

class TransactionalTemplateContentComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    public ?string $name = null;

    public ?string $type = null;

    public ?string $subject = null;

    public ?string $to = null;

    public ?string $cc = null;

    public ?string $bcc = null;

    public ?string $html = null;

    public ?string $structured_html = null;

    protected $listeners = [
        'editorSaved' => 'save',
    ];

    protected function rules(): array
    {
        return [
            'name' => '',
            'type' => '',
            'subject' => 'required',
            'to' => new Delimited('email'),
            'cc' => new Delimited('email'),
            'bcc' => new Delimited('email'),
            'html' => '',
            'structured_html' => '',
        ];
    }

    public function mount(TransactionalMail $transactionalMailTemplate)
    {
        $this->authorize('update', $transactionalMailTemplate);

        $this->template = $transactionalMailTemplate;
        $this->fill(Arr::except($this->template->toArray(), ['to', 'cc', 'bcc']));
        $this->to = $this->template->toString();
        $this->cc = $this->template->ccString();
        $this->bcc = $this->template->bccString();
        $this->subject = $this->template->contentItem->subject;
        $this->html = $this->template->contentItem->html;
        $this->structured_html = $this->template->contentItem->structured_html;

        app(MainNavigation::class)->activeSection()?->add($this->template->name, route('mailcoach.transactionalMails.templates'));
    }

    public function save()
    {
        $this->validate();

        $attributes = [
            'name' => $this->name,
            'type' => $this->type,
            'to' => $this->delimitedToArray($this->to),
            'cc' => $this->delimitedToArray($this->cc),
            'bcc' => $this->delimitedToArray($this->bcc),
        ];

        $this->template->fresh()->update($attributes);

        $this->template->contentItem->update([
            'subject' => $this->subject,
        ]);

        if ($this->template->type !== 'html') {
            $this->template->contentItem->update([
                'html' => $this->html,
                'structured_html' => $this->structured_html,
            ]);

            notify(__mc('Template :template was updated.', ['template' => $this->template->name]));
        }
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.edit')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __mc('Details'),
                'template' => $this->template,
            ]);
    }

    protected function delimitedToArray(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $value)));
    }
}
