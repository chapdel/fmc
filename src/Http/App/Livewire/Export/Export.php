<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationMailsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportCampaignsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportSegmentsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ZipExportJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Export extends Component
{
    use UsesMailcoachModels;

    public Collection $emailLists;
    public Collection $campaigns;
    public Collection $templates;
    public Collection $automations;
    public Collection $automationMails;
    public Collection $transactionalMailTemplates;

    public array $selectedEmailLists = [];
    public array $selectedCampaigns = [];
    public array $selectedTemplates = [];
    public array $selectedAutomations = [];
    public array $selectedAutomationMails = [];
    public array $selectedTransactionalMailTemplates = [];

    public bool $exportStarted = false;

    public function mount()
    {
        $this->emailLists = self::getEmailListClass()::pluck('name', 'id');
        $this->templates = self::getTemplateClass()::pluck('name', 'id');
        $this->automationMails = self::getAutomationMailClass()::pluck('name', 'id');
        $this->transactionalMailTemplates = self::getTransactionalMailTemplateClass()::pluck('name', 'id');
    }

    public function selectAllEmailLists()
    {
        $this->selectedEmailLists = $this->emailLists->keys()->toArray();
    }

    public function selectAllCampaigns()
    {
        $this->selectedCampaigns = $this->campaigns->keys()->toArray();
    }

    public function selectAllTemplates()
    {
        $this->selectedTemplates = $this->templates->keys()->toArray();
    }

    public function selectAllAutomations()
    {
        $this->selectedAutomations = $this->automations->keys()->toArray();
    }

    public function selectAllAutomationMails()
    {
        $this->selectedAutomationMails = $this->automationMails->keys()->toArray();
    }

    public function selectAllTransactionalMailTemplates()
    {
        $this->selectedTransactionalMailTemplates = $this->transactionalMailTemplates->keys()->toArray();
    }

    public function export()
    {
        Cache::forget('export-status');

        $path = Storage::disk(config('mailcoach.export_disk'))->path('export');

        File::deleteDirectory($path);
        File::ensureDirectoryExists($path);

        Bus::chain([
            new ExportEmailListsJob($path, $this->selectedEmailLists),
            new ExportSubscribersJob($path, $this->selectedEmailLists),
            new ExportTagsJob($path, $this->selectedEmailLists),
            new ExportSegmentsJob($path, $this->selectedEmailLists),
            new ExportTemplatesJob($path, $this->selectedTemplates),
            new ExportCampaignsJob($path, $this->selectedCampaigns),
            new ExportAutomationsJob($path, $this->selectedAutomations),
            new ExportAutomationMailsJob($path, $this->selectedAutomationMails),
            new ExportTransactionalMailTemplatesJob($path, $this->selectedTransactionalMailTemplates),
            new ZipExportJob($path),
        ])->dispatch();

        $this->exportStarted = true;
    }

    public function download()
    {
        return response()->download(Storage::disk(config('mailcoach.export_disk'))->path('export/mailcoach-export.zip'));
    }

    public function newExport()
    {
        Cache::forget('export-status');
        File::deleteDirectory(Storage::disk(config('mailcoach.export_disk'))->path('export'));
        $this->exportStarted = false;
    }

    public function render()
    {
        $this->campaigns = self::getCampaignClass()::whereIn('email_list_id', $this->selectedEmailLists)->orWhereNull('email_list_id')->pluck('name', 'id');
        $this->automations = self::getAutomationClass()::whereIn('email_list_id', $this->selectedEmailLists)->orWhereNull('email_list_id')->pluck('name', 'id');
        $exportExists = Storage::disk(config('mailcoach.export_disk'))->exists('export/mailcoach-export.zip');

        return view('mailcoach::app.export', compact('exportExists'))->layout('mailcoach::app.layouts.app');
    }
}
