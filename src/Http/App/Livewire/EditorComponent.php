<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\ValidationRules\Rules\Delimited;

abstract class EditorComponent extends Component
{
    use LivewireFlash;

    public static bool $supportsTemplates = true;

    public static bool $supportsContent = true;

    public HasHtmlContent $model;

    public ?int $templateId = null;
    public ?Template $template = null;

    public array $templateFieldValues = [];
    public string $fullHtml = '';

    public string $emails = '';

    public function mount(HasHtmlContent $model)
    {
        $this->model = $model;

        $this->templateFieldValues = $model->getTemplateFieldValues();

        if ($model instanceof Sendable) {
            $this->template = $model->template;
            $this->templateId = $model->template?->id;
        }

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= '';
        }

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
        $this->renderFullHtml();
    }

    public function renderFullHtml()
    {
        if (! $this->template) {
            $this->fullHtml = $this->templateFieldValues['html'] ?? '';

            return;
        }

        $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
        $this->fullHtml = $templateRenderer->render($this->templateFieldValues);
    }

    public function rules(): array
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        return collect($fieldValues)->mapWithKeys(function ($value, $key) {
            return ["templateFieldValues.{$key}" => ['required', new HtmlRule()]];
        })->toArray();
    }

    public function save()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        if (! $this->model instanceof Template) {
            $this->model->template_id = $this->template?->id;
            $this->model->last_modified_at = now();
        }

        if (! empty($this->rules)) {
            $this->validate($this->rules());
        }

        $this->model->html = $this->fullHtml;
        $this->model->setTemplateFieldValues($fieldValues);

        $this->model->save();

        $this->flash(__('mailcoach - :name was updated.', ['name' => $this->model->name]));

        $this->emit('editorSaved');
    }

    public function sendTest()
    {
        $this->validate([
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ]);

        $this->model->update([
            'template_id' => $this->template->id,
            'html' => $this->fullHtml,
        ]);

        $sanitizedEmails = array_map('trim', explode(',', $this->emails));

        $this->model->sendTestMail($sanitizedEmails);

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
