<?php

namespace Spatie\Mailcoach\Livewire\Editor;

use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mjml\Mjml;

abstract class EditorComponent extends Component
{
    use UsesMailcoachModels;

    public static bool $supportsTemplates = true;

    public static bool $supportsContent = true;

    public HasHtmlContent $model;

    public int|string|null $templateId = null;

    public ?Template $template = null;

    public array $templateFieldValues = [];

    public string $fullHtml = '';

    public string $emails = '';

    public bool $quiet = false;

    public bool $hasError = false;

    public ?CarbonInterface $lastSavedAt = null;

    public bool $autosaveConflict = false;

    public function mount(HasHtmlContent $model)
    {
        $this->model = $model;
        $this->lastSavedAt = $model->updated_at;

        $this->templateFieldValues = $model->getTemplateFieldValues();

        if ($model->hasTemplates()) {
            $this->template = $model->template;
            $this->templateId = $model->template?->id;
        }

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= $this->template?->getHtml() ?? '';
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

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= $this->template?->getHtml() ?? '';
        }
    }

    public function updated()
    {
        $this->renderFullHtml();
    }

    public function renderFullHtml()
    {
        if (! $this->template) {
            $html = $this->templateFieldValues['html'] ?? '';

            if (containsMjml($html)) {
                $this->fullHtml = Mjml::new()->toHtml($html);

                return;
            }

            if (is_array($html)) {
                $html = $html['html'] ?? '';
            }

            $this->fullHtml = $html;

            return;
        }

        $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
        $this->fullHtml = $templateRenderer->render($this->templateFieldValues);

        if (containsMjml($this->template->html)) {
            $this->fullHtml = Mjml::new()->toHtml($this->fullHtml);
        }
    }

    public function rules(): array
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        return collect($fieldValues)->mapWithKeys(function ($value, $key) {
            return ["templateFieldValues.{$key}" => ['required', new HtmlRule()]];
        })->toArray();
    }

    public function autosave()
    {
        if ($this->lastSavedAt && $this->lastSavedAt->timestamp !== $this->model->fresh()->updated_at->timestamp) {
            $this->autosaveConflict = true;

            return;
        }

        $this->saveQuietly();
    }

    public function saveQuietly()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        if ($this->model->hasTemplates()) {
            $this->model->template_id = $this->template?->id;

            if (isset($this->model->attributes['last_modified_at'])) {
                $this->model->last_modified_at = now();
            }
        }

        if (! empty($this->rules)) {
            $this->validate($this->rules());
        }

        $this->hasError = false;

        if (! $this->quiet) {
            try {
                app(RenderTwigAction::class)->execute(htmlspecialchars_decode($this->fullHtml));
            } catch (\Throwable $e) {
                notifyError($e->getMessage());
                $this->hasError = true;

                return;
            }
        }

        $this->model->setHtml($this->fullHtml);
        $this->model->setTemplateFieldValues($fieldValues);
        $this->model->save();
        $this->lastSavedAt = $this->model->updated_at;
        $this->autosaveConflict = false;
        $this->dispatch('editorSavedQuietly');
    }

    public function save()
    {
        if (! $this->isAllowedToSave()) {
            return;
        }

        $this->saveQuietly();

        if (! $this->quiet && ! $this->hasError) {
            notify(__mc(':name was updated.', ['name' => $this->model->fresh()->name]));
        }

        $this->dispatch('editorSaved');
    }

    protected function filterNeededFields(array $fields, ?Template $template): array
    {
        if (! $template) {
            return Arr::only($fields, 'html');
        }

        if (! $template->containsPlaceHolders()) {
            return Arr::only($fields, 'html');
        }

        return Arr::only($fields, Arr::pluck($template->fields(), 'name'));
    }

    protected function isAllowedToSave(): bool
    {
        if ($this->template && ! containsMjml($this->template->getHtml())) {
            return true;
        }

        $mjml = Mjml::new();

        if (! $mjml->canConvert($this->fullHtml)) {
            return false;
        }

        if ($mjml->canConvertWithoutErrors($this->fullHtml)) {
            // @todo show errors
            return true;
        }

        return true;
    }

    abstract public function render();
}
