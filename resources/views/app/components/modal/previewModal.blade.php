@props([
    'html' => '',
    'name' => 'preview',
    'title' => 'Preview',
])
<div>
    <input type="hidden" id="preview-content" value="{{ base64_encode($html) }}">

    @push('modals')
    <x-filament::modal id="preview" slide-over width="2xl">
        <x-slot:heading>
            <p class="mb-2">{{ $title }}</p>
            <x-mailcoach::info class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::info>
        </x-slot:heading>
        <iframe x-data x-effect="
            if (! document.getElementById('{{ $name }}-iframe')) return;
            document.getElementById('{{ $name }}-iframe').src = 'data:text/html;base64,' + document.getElementById('preview-content').value;
        " style="width: 100%; height: 100%;" id="{{ $name }}-iframe"></iframe>
    </x-filament::modal>
    @endpush
</div>
