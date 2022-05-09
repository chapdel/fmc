@props([
    'name',
    'large' => false,
    'title' => null,
    'confirmText' => __('mailcoach - Confirm'),
    'cancelText' =>  __('mailcoach - Cancel'),
    'open' => false,
])
@push('modals')
    <!-- {{ $name }} Modal -->
    <div
        x-data
        @if ($open) x-init="() => $store.modals.open('{{ $name }}')" @endif
        x-show="$store.modals.isOpen('{{ $name }}')"
        style="display: none"
        x-on:keydown.escape.prevent.stop="$store.modals.close('{{ $name }}')"
        role="dialog"
        aria-modal="true"
        x-id="['modal-title']"
        :aria-labelledby="$id('modal-title')"
        class="fixed inset-0 overflow-y-auto z-50"
        {{ $attributes }}
    >
        <!-- Overlay -->
        <div x-show="$store.modals.isOpen('{{ $name }}')" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50"></div>

        <!-- Panel -->
        <div
            x-show="$store.modals.isOpen('{{ $name }}')" x-transition
            x-on:click="$store.modals.close('{{ $name }}')"
            class="relative min-h-screen flex items-center justify-center p-4"
        >
            <div
                x-on:click.stop
                x-trap.noscroll.inert="$store.modals.isOpen('{{ $name }}')"
                class="relative modal-wrapper rounded-sm @if($large) modal-wrapper-lg @endif"
            >
                @if($title)
                    <header class="modal-header">
                        <span class="modal-title">{{ $title }}</span>
                    </header>
                @endif
                <div class="modal-content scrollbar">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
@endpush
