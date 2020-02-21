<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\UpdateTemplateRequest;
use Spatie\Mailcoach\Models\Template;

class TemplatesController
{
    public function index(TemplatesQuery $templatesQuery)
    {
        return view('mailcoach::app.templates.index', [
            'templates' => $templatesQuery->paginate(),
            'totalTemplatesCount' => Template::count(),
        ]);
    }

    public function store(UpdateTemplateRequest $request)
    {
        $template = Template::create([
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
}
