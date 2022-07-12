<x-mailcoach::card>
    <x-mailcoach::help>
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
    
    <x-mailcoach::warning>"Send automation mail" actions in automations will need manual adjustment to the correct Automation Mail. <strong>Automations are imported as paused.</strong></x-mailcoach::warning>


    @if (($steps = Cache::get('import-status', [])) || $importStarted)
        <div class="flex flex-col gap-2" @if(! collect($steps)->where('failed', true)->count() && ! collect($steps)->keys()->contains('Cleanup')) wire:poll.1500ms @endif>
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
            @if(!collect($steps)->where('finished', false)->where('failed', false)->count() && !collect($steps)->keys()->contains('Cleanup'))
                <div class="flex items-center gap-1">
                    <span>Next step is queued...</span>
                    <x-mailcoach::rounded-icon type="warning" icon="fas fa-sync fa-spin" />
                </div>
            @endif
        </div>

        <x-mailcoach::button class="mt-4" wire:click.prevent="clear" :label="__('Start new import')" />
    @else
        <div class="mb-4">
            <input class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" accept=".zip" type="file" wire:model="file" />
            @error('file')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <x-mailcoach::button wire:click.prevent="import" :label="__('Import')" :disabled="!$file" />
            <div wire:loading wire:target="file">
                <style>
                    @keyframes loadingpulse {
                        0%   {transform: scale(.8); opacity: .75}
                        100% {transform: scale(1); opacity: .9}
                    }
                </style>
                <span
                    style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                    class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
                <span class="ml-1 text-gray-700">Uploading...</span>
            </div>
        </div>
    @endif
</x-mailcoach::card>
