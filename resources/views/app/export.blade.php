<div @if ($exportStarted && ! $exportExists) wire:poll.750ms @endif>
    @if ($exportStarted || $exportExists)
        <h1 class="text-xl font-bold mb-6">Export</h1>
        @forelse (Cache::get('export-status', []) as $name => $data)
            <p class="flex items-center gap-1">
                <span>{{ $name }}</span>
                @if ($data['finished'])
                    <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
                @elseif ($data['error'])
                    <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
                    <span> &mdash; {{ $data['error'] }}</span>
                    <x-mailcoach::button-secondary class="mt-8" wire:click.prevent="newExport" :label="__('Create new export')" />
                @else
                    <x-mailcoach::rounded-icon type="warning" icon="fas fa-sync fa-spin" />
                @endif
            </p>
        @empty
            <p>Export started... <x-mailcoach::rounded-icon type="warning" icon="fas fa-sync fa-spin" /></p>
        @endforelse

        @if ($exportExists)
            <div class="my-4 flex items-center gap-4">
                <x-mailcoach::button wire:click.prevent="download" :label="__('Download export')" />
                <p class="text-sm">Created on {{ \Illuminate\Support\Facades\Date::createFromTimestamp(Storage::disk(config('mailcoach.export_disk'))->lastModified('export/mailcoach-export.zip'))->format('Y-m-d H:i:s') }}</p>
            </div>
            <x-mailcoach::button-secondary class="mt-8" wire:click.prevent="newExport" :label="__('Create new export')" />
        @endif
    @else
        <h1 class="text-xl font-bold mb-6">Choose which data you want to export</h1>

        <x-mailcoach::help class="mb-6">
            <p>Mailcoach can export (almost) all data to be used in a different Mailcoach instance (either self-hosted or hosted on mailcoach.cloud).</p>
            <p>The exporter will <strong>not export</strong> the following data:</p>
            <ul class="list-disc ml-4">
                <li>Users</li>
                <li>Individual send data</li>
                <li>Clicks / Opens / Unsubscribes (it will only export the calculated statistics)</li>
                <li>Any uploaded media</li>
            </ul>
        </x-mailcoach::help>

        <h2 class="text-lg">Email lists <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllEmailLists">All</a></h2>
        <p class="mb-3">This includes subscribers, tags & segments</p>
        <div class="flex flex-col gap-4 mb-6">
            @foreach($emailLists as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedEmailList-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedEmailLists"
                />
            @endforeach
        </div>

        <h2 class="text-lg mb-3">Campaigns <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllCampaigns">All</a></h2>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($campaigns as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedCampaign-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedCampaigns"
                />
            @empty
                <x-mailcoach::help>No campaigns found, campaigns require their email list to be exported as well.</x-mailcoach::help>
            @endforelse
        </div>

        <h2 class="text-lg mb-3">Templates <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllTemplates">All</a></h2>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($templates as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedTemplate-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedTemplates"
                />
            @empty
                <x-mailcoach::help>No templates found.</x-mailcoach::help>
            @endforelse
        </div>

        <h2 class="text-lg">Automations <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllAutomations">All</a></h2>
        <p class="mb-3">This includes triggers, actions & action-subscriber state</p>

        <x-mailcoach::warning class="mb-3">"Send automation mail" actions will need manual adjustment to the correct Automation Mail.</x-mailcoach::warning>

        <div class="flex flex-col gap-4 mb-6">
            @forelse($automations as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedAutomation-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedAutomations"
                />
            @empty
                <x-mailcoach::help>No automations found, automations require their email list to be exported as well.</x-mailcoach::help>
            @endforelse
        </div>

        <h2 class="text-lg mb-3">Automation Mails <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllAutomationMails">All</a></h2>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($automationMails as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedAutomationMail-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedAutomationMails"
                />
            @empty
                <x-mailcoach::help>No automation mails found.</x-mailcoach::help>
            @endforelse
        </div>

        <h2 class="text-lg mb-3">Transactional Mail Templates <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllTransactionalMailTemplates">All</a></h2>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($transactionalMailTemplates as $id => $name)
                <x-mailcoach::checkbox-field
                    name="selectedTransactionalMailTemplate-{{ $id }}"
                    value="{{ $id }}"
                    :label="$name"
                    wire:model="selectedTransactionalMailTemplates"
                />
            @empty
                <x-mailcoach::help>No transactional mail templates found.</x-mailcoach::help>
            @endforelse
        </div>

        <x-mailcoach::button wire:click.prevent="export" wire:loading.attr="disabled" :label="__('Export')" />
    @endif
</div>
