<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;
use Spatie\Mailcoach\Livewire\DataTableComponent;
use Spatie\Mailcoach\Livewire\LivewireFlash;

class TransactionalMailsComponent extends DataTableComponent
{
    use LivewireFlash;

    public function duplicateTemplate(int $id)
    {
        $template = self::getTransactionalMailClass()::find($id);

        $this->authorize('create', self::getTransactionalMailClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $template->replicate();
        $duplicateTemplate->uuid = Str::uuid();
        $duplicateTemplate->name .= '-copy';
        $duplicateTemplate->save();

        flash()->success(__mc('Email :name was duplicated.', ['name' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }

    public function deleteTemplate(int $id)
    {
        $template = self::getTransactionalMailClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->flash(__mc('Email :name was deleted.', ['name' => $template->name]));
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    public function getView(): string
    {
        return 'mailcoach::app.transactionalMails.templates.index';
    }

    public function getData(Request $request): array
    {
        $this->authorize('viewAny', static::getTransactionalMailClass());

        return [
            'templates' => (new TransactionalMailTemplateQuery($request))->paginate($request->per_page),
            'templatesCount' => self::getTransactionalMailClass()::count(),
        ];
    }
}
