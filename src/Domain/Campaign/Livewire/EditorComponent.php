<?php

namespace Spatie\Mailcoach\Domain\Campaign\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\ValidationRules\Rules\Delimited;

abstract class EditorComponent extends Component
{
    use LivewireFlash;

    public Sendable $sendable;

    public ?int $templateId = null;
    public ?Template $template = null;

    public array $templateFieldValues = [];
    public string $fullHtml = '';

    public string $emails = '';

    public function mount(Sendable $sendable)
    {
        $this->sendable = $sendable;

        $this->templateFieldValues = $sendable->getTemplateFieldValues();
        $this->template = $sendable->template;
        $this->templateId = $sendable->template?->id;
        $this->renderFullHtml();
    }

    public function updatingTemplateId(?int $templateId)
    {
        if ($templateId === 0) {
            $this->template = null;

            return;
        }

        $this->template = Template::find($templateId);

        if (! $this->template->containsPlaceHolders()) {
            $this->templateFieldValues['html'] = $this->template->getHtml();
        }
    }

    public function updated()
    {
        if (! $this->template) {
            $this->fullHtml = $this->templateFieldValues['html'] ?? '';

            return;
        }

        $this->renderFullHtml();
    }

    public function renderFullHtml()
    {
        $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
        $this->fullHtml = $templateRenderer->render($this->templateFieldValues);
    }

    public function save()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        $this->sendable->template_id = $this->template?->id;
        $this->sendable->last_modified_at = now();
        $this->sendable->html = $this->fullHtml;
        $this->sendable->setTemplateFieldValues($fieldValues);

        $this->sendable->save();

        match (true) {
            $this->sendable instanceof Campaign => $this->flash(__('mailcoach - Campaign :campaign was updated.', ['campaign' => $this->sendable->name])),
            $this->sendable instanceof AutomationMail => $this->flash(__('mailcoach - Email :name was updated.', ['name' => $this->sendable->name])),
        };
    }

    public function sendTest()
    {
        $this->validate([
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ]);

        $this->sendable->update([
            'template_id' => $this->template->id,
            'html' => $this->fullHtml,
        ]);

        $sanitizedEmails = array_map('trim', explode(',', $this->emails));

        $this->sendable->sendTestMail($sanitizedEmails);

        cache()->put(
            'mailcoach-test-email-addresses',
            $this->emails,
            (int)CarbonInterval::month()->totalSeconds,
        );

        $this->flashSuccessMessage($sanitizedEmails);

        // close modal
    }


    protected function flashSuccessMessage(array $emails): void
    {
        if (count($emails) > 1) {
            $emailCount = count($emails);

            //flash()->success(__('mailcoach - A test email was sent to :count addresses.', ['count' => $emailCount]));

            return;
        }

        // flash()->success(__('mailcoach - A test email was sent to :email.', ['email' => $emails[0]]));
    }

    protected function filterNeededFields(array $fields, ?Template $template): array
    {
        if (! $template) {
            return Arr::only($fields, 'html');
        }

        if (! $template->containsPlaceHolders()) {
            return Arr::only($fields, 'html');
        }

        return Arr::only($fields, $template->placeHolderNames());
    }

    abstract public function render();
}
