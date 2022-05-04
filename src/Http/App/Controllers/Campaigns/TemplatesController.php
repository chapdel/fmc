<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\UpdateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Resources\TemplateResource;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Requests\TemplateRequest;

class TemplatesController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function index(TemplatesQuery $templatesQuery)
    {
        $this->authorize('viewAny', static::getTemplateClass());

        return view('mailcoach::app.campaigns.templates.index', [
            'templates' => $templatesQuery->paginate(),
            'totalTemplatesCount' => $this->getTemplateClass()::count(),
        ]);
    }

    public function show(Template $template)
    {
        $this->authorize('view', $template);

        return new TemplateResource($template);
    }

    public function edit(Template $template)
    {
        $this->authorize('update', $template);

        return view('mailcoach::app.campaigns.templates.edit', [
            'template' => $template,
        ]);
    }

    public function update(
        Template $template,
        TemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $this->authorize('update', $template);

        $updateTemplateAction->execute($template, $request->validated());

        flash()->success(__('mailcoach - Template :template was updated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates');
    }

    public function duplicate(Template $template)
    {
        $this->authorize('create', static::getTemplateClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $this->getTemplateClass()::create([
            'name' => __('mailcoach - Duplicate of') . ' ' . $template->name,
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        flash()->success(__('mailcoach - Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }
}
