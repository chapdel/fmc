@props([
    'html' => '',
    'name' => 'preview',
    'title' => 'Preview',
])
@teleport('body')
<div>
    <x-filament::modal id="preview" slide-over width="2xl">
        <x-slot:heading>
            <p class="mb-2">{{ $title }}</p>
            <x-mailcoach::info class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::info>
        </x-slot:heading>
        <iframe
            src="data:text/html;base64,{{ base64_encode($html) }}"
            style="width: 100%; height: 100%;" id="{{ $name }}-iframe"></iframe>
    </x-filament::modal>
</div>
@endteleport
