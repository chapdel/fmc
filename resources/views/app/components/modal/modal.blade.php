@props([
    'name',
    'medium' => false,
    'large' => false,
    'title' => null,
    'confirmText' => __mc('Confirm'),
    'cancelText' =>  __mc('Cancel'),
    'open' => false,
    'dismissable' => false,
    'slideOver' => false,
])
@push('modals')
    <x-filament::modal
        id="{{ $name }}"
        :slide-over="$slideOver"
        :close-by-clicking-away="$dismissable"
        :close-button="true"
        x-init="window.location.hash === '#{{ $name }}' ? $dispatch('open-modal', { id: '{{ $name }}' }) : null"
        x-on:close-modal.window="history.replaceState(null, null, ' ')"
        x-on:open-modal.window="(event) => window.location.hash = event.detail.id"
        {{ $attributes }}
        :header="$title"
    >
        {{ $slot }}
    </x-filament::modal>
@endpush
