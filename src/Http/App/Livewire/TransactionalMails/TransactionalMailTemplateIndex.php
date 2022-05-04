<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;

class TransactionalMailTemplateIndex extends DataTable
{
    public function deleteTemplate(int $id)
    {
        $template = self::getTransactionalMailTemplateClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Template :template was deleted.', ['template' => $template->name]),
        ]);
    }

    public function getTitle(): string
    {
        return __('mailcoach - Transactional log');
    }

    public function getView(): string
    {
        return 'mailcoach::app.transactionalMails.templates.index';
    }

    public function getData(): array
    {
        $this->authorize('viewAny', static::getTransactionalMailTemplateClass());

        return [
            'templates' => (new TransactionalMailTemplateQuery(request()))->paginate(),
            'templatesCount' => self::getTransactionalMailTemplateClass()::count(),
        ];
    }
}
