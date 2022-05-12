<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;

class TransactionalTemplates extends DataTable
{
    use LivewireFlash;

    public function duplicateTemplate(int $id)
    {
        $template = self::getTransactionalMailTemplateClass()::find($id);

        $this->authorize('create', self::getTransactionalMailTemplateClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $template->replicate()->save();

        flash()->success(__('mailcoach - Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }

    public function deleteTemplate(int $id)
    {
        $template = self::getTransactionalMailTemplateClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->flash(__('mailcoach - Template :template was deleted.', ['template' => $template->name]));
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
