<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\UpdateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Resources\TemplateResource;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\TemplateRequest;

class TemplatesController
{
    use UsesMailcoachModels;

    public function index(TemplatesQuery $templatesQuery)
    {
        return view('mailcoach::app.templates.index', [
            'templates' => $templatesQuery->paginate(),
            'totalTemplatesCount' => $this->getTemplateClass()::count(),
        ]);
    }

    public function show(Template $template)
    {
        return new TemplateResource($template);
    }

    public function store(TemplateRequest $request, CreateTemplateAction $createTemplateAction)
    {
        $template = $createTemplateAction->execute($request->validated());

        flash()->success(__('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $template);
    }

    public function edit(Template $template)
    {
        return view('mailcoach::app.templates.edit', [
            'template' => $template,
        ]);
    }

    public function update(
        Template $template,
        TemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $updateTemplateAction->execute($template, $request->validated());

        flash()->success(__('Template :template was updated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        flash()->success(__('Template :template was deleted.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates');
    }

    public function duplicate(Template $template)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $this->getTemplateClass()::create([
            'name' => __('Duplicate of') . ' ' . $template->name,
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        flash()->success(__('Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }
}
