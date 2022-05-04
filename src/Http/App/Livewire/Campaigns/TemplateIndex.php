<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;

class TemplateIndex extends DataTable
{
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
        return 'mailcoach::app.campaigns.templates.index';
    }

    public function getData(): array
    {
        return [
            'templates' => (new TemplatesQuery(request()))->paginate(),
            'totalTemplatesCount' => self::getTemplateClass()::count(),
        ];
    }
}
