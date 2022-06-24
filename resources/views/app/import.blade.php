<div>
    <h2 class="text-lg mb-4">Import Mailcoach data from a different instance</h2>

    <x-mailcoach::help class="mb-6">
        <p>Mailcoach can import (almost) all data to be used in a different Mailcoach instance (either self-hosted or hosted on mailcoach.cloud).</p>
        <p>The import will <strong>not import</strong> the following data:</p>
        <ul class="list-disc ml-4">
            <li>Users</li>
            <li>Individual send data</li>
            <li>Clicks / Opens / Unsubscribes (it will only export the calculated statistics)</li>
            <li>Any uploaded media</li>
        </ul>
        <p>Imports can always be reuploaded if something goes wrong.</p>
    </x-mailcoach::help>

    <x-mailcoach::warning class="mb-3">"Send automation mail" actions in automations will need manual adjustment to the correct Automation Mail. <strong>Automations are imported as paused.</strong></x-mailcoach::warning>

    @if (($steps = Cache::get('import-status', [])) || $importStarted)
        <div class="flex flex-col gap-2" @if(! collect($steps)->where('failed', true)->count()) wire:poll.1500ms @endif>
            @forelse ($steps as $name => $data)
                <p class="flex items-center gap-1">
                    <span>{{ $name }}</span>
                    @if ($data['finished'])
                        <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
                        @if($data['total'])
                            <span>&mdash; {{ number_format($data['total']) }} rows</span>
                        @endif
                    @elseif ($data['failed'])
                        <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
                        <span> &mdash; {{ $data['message'] }}</span>
                    @else
                        <x-mailcoach::rounded-icon type="warning" icon="fas fa-sync fa-spin" />
                        <span>({{ round($data['progress'] * 100, 2) }}%)</span>
                    @endif
                </p>
            @empty
                <p>Import queued...</p>
            @endforelse
        </div>

        <x-mailcoach::button class="mt-4" wire:click.prevent="clear" :label="__('Start new import')" />
    @else
        <div class="mb-4">
            <input accept=".zip" type="file" wire:model="file" />
            @error('file')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <x-mailcoach::button wire:click.prevent="import" :label="__('Import')" />
    @endif
</div>
