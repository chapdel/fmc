<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;

class Templates extends DataTable
{
    public function duplicateTemplate(int $id)
    {
        $template = self::getTemplateClass()::find($id);

        $this->authorize('create', self::getTemplateClass());

        $duplicateTemplate = self::getTemplateClass()::create([
            'name' => __('mailcoach - Duplicate of').' '.$template->name,
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        flash()->success(__('mailcoach - Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }

    public function deleteTemplate(int $id)
    {
        $template = self::getTemplateClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->flash(__('mailcoach - Template :template was deleted.', ['template' => $template->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Templates');
    }

    public function getView(): string
    {
        return 'mailcoach::app.templates.index';
    }

    public function getData(Request $request): array
    {
        return [
            'templates' => (new TemplatesQuery($request))->paginate(),
            'totalTemplatesCount' => self::getTemplateClass()::count(),
        ];
    }
}
