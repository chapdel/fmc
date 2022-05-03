<?php

namespace Spatie\Mailcoach\Domain\Campaign\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\ValidationRules\Rules\Delimited;

class TextAreaEditorComponent extends Component
{
    public Campaign $campaign;

    public ?int $templateId = null;
    public ?Template $template = null;

    public array $fields = [];
    public string $fullHtml = '';

    public string $emails = '';

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->fields = $campaign->fields->toArray();
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
            $this->fields['html'] = $this->template->getHtml();
        }
    }

    public function updated()
    {
        if (! $this->template) {
            $this->fullHtml = $this->fields['html'] ?? '';

            return;
        }

        $html = $this->template->html;

        foreach ($this->template->placeHolderNames() as $placeHolderName) {
            $html = str_replace('[[[' . $placeHolderName . ']]]', $this->fields[$placeHolderName] ?? '', $html);
        }

        $this->fullHtml = $html;
    }

    public function save()
    {
        $this->campaign->update([
            'template_id' => $this->template?->id,
            'fields' => $this->filterNeededFields($this->fields, $this->template),
            'last_modified_at' => now(),
            'html' => $this->fullHtml,
        ]);

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

        cache()->put('mailcoach-test-email-addresses', $this->emails, (int)CarbonInterval::month()->totalSeconds);

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

    public function render()
    {
        return view('mailcoach::editors.livewire.textEditor');
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
}
