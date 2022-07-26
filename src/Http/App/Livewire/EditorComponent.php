<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

abstract class EditorComponent extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public static bool $supportsTemplates = true;

    public static bool $supportsContent = true;

    public HasHtmlContent $model;

    public int|string|null $templateId = null;
    public ?Template $template = null;

    public array $templateFieldValues = [];
    public string $fullHtml = '';

    public string $emails = '';

    public function mount(HasHtmlContent $model)
    {
        $this->model = $model;

        $this->templateFieldValues = $model->getTemplateFieldValues();

        if ($model instanceof Sendable || $model instanceof TransactionalMailTemplate) {
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

    public function updatingTemplateId(int|string|null $templateId)
    {
        if (! $templateId) {
            $this->template = null;

            return;
        }

        $this->template = self::getTemplateClass()::find($templateId);

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

    public function saveQuietly()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        if (! $this->model instanceof Template) {
            $this->model->template_id = $this->template?->id;

            if (isset($this->model->attributes['last_modified_at'])) {
                $this->model->last_modified_at = now();
            }
        }

        if (! empty($this->rules)) {
            $this->validate($this->rules());
        }

        $this->model->setHtml($this->fullHtml);
        $this->model->setTemplateFieldValues($fieldValues);
        $this->model->save();
    }

    public function save()
    {
        $this->saveQuietly();

        $this->flash(__('mailcoach - :name was updated.', ['name' => $this->model->name]));

        $this->emit('editorSaved');
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
