<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\MailersQuery;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Mailers extends DataTable
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public function getTitle(): string
    {
        return __('Mailers');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.mailers.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __('Mailers'),
        ];
    }

    public function markMailerDefault(int $id)
    {
        self::getMailerClass()::query()->update(['default' => false]);

        $mailer = self::getMailerClass()::find($id);

        if (! $mailer->ready_for_use) {
            $this->flashError(__('Mailer :mailer is not ready for use', ['mailer' => $mailer->name]));

            return;
        }

        $mailer->update(['default' => true]);

        $this->flash(__('Mailer :mailer marked as default', ['mailer' => $mailer->name]));
    }

    public function deleteMailer(int $id)
    {
        $mailer = self::getMailerClass()::find($id);

        $mailer->delete();

        $this->flash(__('Mailer :mailer successfully deleted', ['mailer' => $mailer->name]));
    }

    public function getData(Request $request): array
    {
        return [
            'mailers' => (new MailersQuery($request))->paginate(),
            'totalMailersCount' => self::getMailerClass()::count(),
        ];
    }
}
