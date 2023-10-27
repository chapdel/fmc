@props([
    'id' => 'preview',
    'html' => '',
    'title' => 'Preview',
])
<x-filament::modal id="{{ $id }}" slide-over width="3xl">
    <x-slot:heading>
        <p class="mb-2">{{ $title }}</p>
        <x-mailcoach::info class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::info>
    </x-slot:heading>
    <x-mailcoach::web-view :html="$html"></x-mailcoach::web-view>
</x-filament::modal>
