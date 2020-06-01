<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\UpdateTemplateRequest;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

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

    public function store(UpdateTemplateRequest $request)
    {
        $template = $this->getTemplateClass()::create([
            'name' => $request->name,
            'html' => $request->html ?? '',
            'structured_html' => $request->structured_html,
        ]);

        flash()->success("Template {$template->name} was created.");

        return redirect()->route('mailcoach.templates.edit', $template);
    }

    public function edit(Template $template)
    {
        return view('mailcoach::app.templates.edit', [
            'template' => $template,
        ]);
    }

    public function update(Template $template, UpdateTemplateRequest $request)
    {
        $template->update([
            'name' => $request->name,
            'html' => $request->html ?? '',
            'structured_html' => $request->structured_html,
        ]);

        flash()->success("Template {$template->name} was updated.");

        return redirect()->route('mailcoach.templates');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        flash()->success("Template {$template->name} was deleted.");

        return redirect()->route('mailcoach.templates');
    }

    public function duplicate(Template $template)
    {
        /** @var \Spatie\Mailcoach\Models\Template $duplicateTemplate */
        $duplicateTemplate = $this->getTemplateClass()::create([
            'name' => "Duplicate of {$template->name}",
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        flash()->success("Template {$template->name} was duplicated.");

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }
}
