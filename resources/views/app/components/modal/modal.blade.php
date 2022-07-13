@props([
    'name',
    'medium' => false,
    'large' => false,
    'title' => null,
    'confirmText' => __('mailcoach - Confirm'),
    'cancelText' =>  __('mailcoach - Cancel'),
    'open' => false,
    'dismissable' => false,
])
@push('modals')
    <!-- {{ $name }} Modal -->
    <div
        x-data
        @if ($open) x-init="() => $store.modals.open('{{ $name }}')" @endif
        x-show="$store.modals.isOpen('{{ $name }}')"
        style="display: none"
        x-on:keydown.escape.prevent.stop="$store.modals.close('{{ $name }}')"
        x-on:keydown.window.escape.prevent.stop="$store.modals.close('{{ $name }}')"
        role="dialog"
        aria-modal="true"
        id="modal-{{ $name }}"
        x-id="['modal-title']"
        :aria-labelledby="$id('modal-title')"
        class="fixed inset-0 overflow-y-auto z-50"
        @modal-closed.window="$store.modals.close($event.detail.modal)"
        x-ref="modal"
        {{ $attributes }}
    >
        <!-- Overlay -->
        <div x-show="$store.modals.isOpen('{{ $name }}')" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50"></div>

        <!-- Panel -->
        <div
            x-show="$store.modals.isOpen('{{ $name }}')" x-transition
            @if($dismissable)
            x-on:click.prevent="$store.modals.close('{{ $name }}')"
            @else
            x-on:click="
                $refs.modal.classList.add('animate-scale');
                setTimeout(() => $refs.modal.classList.remove('animate-scale'), 300);
            "
            @endif
            class="relative h-screen min-h-screen flex items-center justify-center p-4"
        >
            <div
                x-on:click.stop
                x-trap.noscroll.inert="$store.modals.isOpen('{{ $name }}')"
                class="relative modal-wrapper rounded-sm @if($medium) modal-wrapper-md @endif @if($large) h-full modal-wrapper-lg @endif"
            >   
            <div class="modal">
                @if($title)
                    <header class="modal-header flex items-center justify-between">
                        <span class="modal-title">{{ $title }}</span>
                        <button class="modal-close" x-on:click.prevent="$store.modals.close('{{ $name }}')">
                            <i class="far fa-times"></i>
                        </button>
                    </header>
                @endif
                <div class="modal-content scrollbar">
                    {{ $slot }}
                </div>
            </div>
            </div>
        </div>
    </div>
@endpush
