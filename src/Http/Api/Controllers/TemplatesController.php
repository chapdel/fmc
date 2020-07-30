<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Spatie\Mailcoach\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Http\Api\Resources\TemplateResource;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\TemplateRequest;

class TemplatesController
{
    public function index(TemplatesQuery $templatesQuery)
    {
        $templates = $templatesQuery->paginate();

        return TemplateResource::collection($templates);
    }

    public function store(
        TemplateRequest $templateRequest,
        CreateTemplateAction $createTemplateAction
    ) {
        $template = $createTemplateAction->execute($templateRequest->validated());

        return new TemplateResource($template);
    }
}
