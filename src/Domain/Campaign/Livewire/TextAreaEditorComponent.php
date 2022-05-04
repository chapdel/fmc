<?php

namespace Spatie\Mailcoach\Domain\Campaign\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\ValidationRules\Rules\Delimited;

class TextAreaEditorComponent extends Component
{
    public Campaign $campaign;

    public ?int $templateId = null;
    public ?Template $template = null;

    public array $templateFieldValues = [];
    public string $fullHtml = '';

    public string $emails = '';

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->templateFieldValues = $campaign->getTemplateFieldValues();
        $this->template = $campaign->template;
        $this->templateId = $campaign->template?->id;
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

        $templateRenderer = (new TemplateRenderer($this->template->html));

        $this->fullHtml = $templateRenderer->render($this->templateFieldValues);;
    }

    public function save()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        $this->campaign->template_id = $this->template?->id;
        $this->campaign->last_modified_at = now();
        $this->campaign->html = $this->fullHtml;
        $this->campaign->setTemplateFieldValues($fieldValues);

        $this->campaign->save();

        // flash()->success(__('mailcoach - Campaign :campaign was updated.', ['campaign' => $campaign->name]));
    }

    public function sendTest()
    {
        $this->validate([
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ]);

        $this->campaign->update([
            'template_id' => $this->template->id,
            'html' => $this->fullHtml,
        ]);

        $sanitizedEmails = array_map('trim', explode(',', $this->emails));

        $this->campaign->sendTestMail($sanitizedEmails);

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

    public function render()
    {
        return view('mailcoach::editors.livewire.textEditor');
    }
}
