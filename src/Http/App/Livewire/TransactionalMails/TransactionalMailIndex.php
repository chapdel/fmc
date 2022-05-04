<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailQuery;

class TransactionalMailIndex extends DataTable
{
    public string $sort = '-created_at';

    public function deleteTransactionalMail(int $id)
    {
        $transactionalMail = self::getTransactionalMailClass()::find($id);

        $this->authorize('delete', $transactionalMail);

        $transactionalMail->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - The mail was removed from the log'),
        ]);
    }

    public function getTitle(): string
    {
        return __('mailcoach - Transactional log');
    }

    public function getView(): string
    {
        return 'mailcoach::app.transactionalMails.index';
    }

    public function getData(): array
    {
        $this->authorize('viewAny', static::getTransactionalMailClass());

        return [
            'transactionalMails' => (new TransactionalMailQuery(request()))->paginate(),
            'transactionalMailsCount' => self::getTransactionalMailClass()::count(),
        ];
    }
}
