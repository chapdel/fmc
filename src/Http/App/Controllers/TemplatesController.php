<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\UpdateTemplateRequest;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Models\Upload;

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
            'json' => json_decode($request->json),
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
            'json' => json_decode($request->json),
        ]);

        flash()->success("Template {$template->name} was updated.");

        return redirect()->route('mailcoach.templates');
    }

    public function addUpload(Template $template, Request $request)
    {
        $upload = Upload::create();
        $media = $upload->addMediaFromRequest('file')->toMediaCollection('default', config('mailcoach.editor.uploads.disk_name'));

        $upload->templates()->attach($template);

        return response()->json(['url' => $media->getFullUrl('image')]);
    }

    public function destroy(Template $template)
    {
        $template->delete();

        flash()->success("Template {$template->name} was deleted.");

        return redirect()->route('mailcoach.templates');
    }
}
