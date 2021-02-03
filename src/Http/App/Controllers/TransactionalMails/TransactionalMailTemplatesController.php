<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\CreateTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\UpdateTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;
use Spatie\Mailcoach\Http\App\Requests\TransactionalMails\TransactionalMailTemplateRequest;

class TransactionalMailTemplatesController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function index(TransactionalMailTemplateQuery $transactionalMailTemplateQuery)
    {
        $this->authorize('viewAny', TransactionalMailTemplate::class);

        return view('mailcoach::app.transactionalMails.templates.index', [
            'templates' => $transactionalMailTemplateQuery->paginate(),
            'templatesQuery' => $transactionalMailTemplateQuery,
            'templatesCount' => $this->getTransactionalMailTemplateClass()::count(),
        ]);
    }

    public function store(TransactionalMailTemplateRequest $request, CreateTemplateAction $createTemplateAction)
    {
        $this->authorize('create', TransactionalMailTemplate::class);

        $template = $createTemplateAction->execute($request->validated());

        flash()->success(__('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $template);
    }

    public function edit(TransactionalMailTemplate $template)
    {
        ray($template);
        $this->authorize('update', $template);

        return view('mailcoach::app.transactionalMails.templates.edit', [
            'template' => $template,
        ]);
    }

    public function update(
        TransactionalMailTemplate $template,
        TransactionalMailTemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $this->authorize('update', $template);

        $updateTemplateAction->execute($template, $request->validated());

        flash()->success(__('Template :template was updated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates');
    }

    public function destroy(TransactionalMailTemplate $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        flash()->success(__('Template :template was deleted.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates');
    }

    public function duplicate(TransactionalMailTemplate $template)
    {
        $this->authorize('create', TransactionalMailTemplate::class);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $this->getTransactionalMailTemplateClass()::create([
            'name' => __('Duplicate of') . ' ' . $template->name,
            // TODO: add other attributes
        ]);

        flash()->success(__('Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }
}
