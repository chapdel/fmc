@props([
    'id' => 'preview',
    'html' => '',
    'title' => 'Preview',
])
@teleport('body')
    <x-filament::modal id="{{ $id }}" slide-over width="2xl">
        <x-slot:heading>
            <p class="mb-2">{{ $title }}</p>
            <x-mailcoach::info class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::info>
        </x-slot:heading>
        <iframe
            src="data:text/html;base64,{{ base64_encode($html) }}"
            style="width: 100%; height: 100%;" id="{{ $id }}-iframe"></iframe>
    </x-filament::modal>
@endteleport
