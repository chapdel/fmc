<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;

class TemplateIndex extends DataTable
{
    public function deleteTemplate(int $id)
    {
        $template = self::getTemplateClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Template :template was deleted.', ['template' => $template->name]),
        ]);
    }

    public function render()
    {
        parent::render();

        return view('mailcoach::app.campaigns.templates.index', [
            'templates' => (new TemplatesQuery(request()))->paginate(),
            'totalTemplatesCount' => self::getTemplateClass()::count(),
        ])->layout('mailcoach::app.layouts.main', [
            'title' => __('mailcoach - Templates'),
        ]);
    }
}
