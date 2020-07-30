<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Spatie\Mailcoach\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Actions\Templates\UpdateTemplateAction;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Resources\TemplateResource;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\TemplateRequest;
use Spatie\Mailcoach\Models\Template;

class TemplatesController
{
    use RespondsToApiRequests;

    public function index(TemplatesQuery $templatesQuery)
    {
        $templates = $templatesQuery->paginate();

        return TemplateResource::collection($templates);
    }

    public function show(Template $template)
    {
        return new TemplateResource($template);
    }

    public function store(
        TemplateRequest $request,
        CreateTemplateAction $createTemplateAction
    ) {
        $template = $createTemplateAction->execute($request->validated());

        return new TemplateResource($template);
    }

    public function update(
        Template $template,
        TemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $template = $updateTemplateAction->execute($template, $request->validated());

        return new TemplateResource($template);
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return $this->respondOk();
    }
}
