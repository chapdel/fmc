@pushonce('scripts')
    <script>
        document.addEventListener('livewire:init', function () {
            setInterval(() => @this.autosave(), 20000);
        });
    </script>
@endpushonce

<x-mailcoach::card class="flex flex-col gap-y-4 p-6">
    @foreach ($contentItems as $index => $contentItem)
        <div
            @if ($contentItems->count() > 1) class="border border-indigo-700/10 rounded bg-indigo-200/10 p-6 mb-6" @endif
            x-data="{
                collapsed: false,
            }"
        >
            <div class="flex items-center relative z-10 pointer-events-none" x-bind:class="collapsed ? '' : '{{ $contentItems->count() > 1 ? 'mb-6' : '-mb-6' }}'" x-cloak>
                @if ($contentItems->count() > 1)
                    <div class="flex items-center gap-x-2 pointer-events-auto">
                        <button type="button" x-tooltip="'{{ __mc('Expand') }}'" x-show="collapsed" x-on:click="collapsed = !collapsed">
                            <x-icon class="w-5 h-5" name="heroicon-o-chevron-up" />
                        </button>
                        <button type="button" x-tooltip="'{{ __mc('Collapse') }}'" x-show="!collapsed" x-on:click="collapsed = !collapsed">
                            <x-icon class="w-5 h-5" name="heroicon-o-chevron-down" />
                        </button>
                        <h3 class="markup-h3">
                            <span class="w-6 h-6 relative -top-[4px] rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                                {{ $index + 1 }}
                            </span>
                            {{ $contentItem->subject }}
                        </h3>
                    </div>
                @endif
                <div class="ml-auto flex items-center gap-x-4 pointer-events-auto">
                    <button type="button" x-tooltip="'{{ __mc('Add split test') }}'" wire:click="addSplitTest('{{ $contentItem->uuid }}')">
                        <x-icon class="w-5 h-5" name="heroicon-o-document-plus" />
                    </button>
                    <button type="button" x-tooltip="'{{ __mc('Preview') }}'" x-on:click.prevent="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })">
                        <x-icon class="w-5 h-5" name="heroicon-o-eye" />
                    </button>
                    @if ($contentItems->count() > 1)
                        <x-mailcoach::confirm-button on-confirm="() => $wire.deleteSplitTest('{{ $contentItem->uuid }}')" confirm-text="{{ __mc('Are you sure you want to delete this split test?') }}" x-tooltip="'{{ __mc('Delete') }}'">
                            <x-icon class="w-5 h-5 link-danger" name="heroicon-o-trash" />
                        </x-mailcoach::confirm-button>
                    @endif
                </div>
            </div>

            <div class="form-grid" wire:ignore x-show="!collapsed" x-collapse>
                <form
                    class="card-grid"
                    method="POST"
                    data-dirty-check
                    wire:submit="save"
                    @keydown.prevent.window.cmd.s="$wire.call('save')"
                    @keydown.prevent.window.ctrl.s="$wire.call('save')"
                >
                    @csrf

                    <x-mailcoach::text-field :label="__mc('Subject')" name="subject" wire:model="content.{{ $contentItem->uuid }}.subject" />
                </form>

                @livewire(config('mailcoach.content_editor'), [
                    'model' => $contentItem,
                ])
            </div>
        </div>
    @endforeach

    <x-mailcoach::form-buttons>
        <div class="flex items-center gap-x-4">
            <x-mailcoach::button
                @keydown.prevent.window.cmd.s="$wire.call('save')"
                @keydown.prevent.window.ctrl.s="$wire.call('save')"
                wire:click.prevent="save"
                :label="__mc('Save content')"
            />

            <x-mailcoach::replacer-help-texts :model="$contentItem" />
        </div>

        @if ($this->autosaveConflict)
            <x-mailcoach::warning class="mt-4">
                {{ __mc('Autosave disabled, the content was saved somewhere else. Refresh the page to get the latest content or save manually to override.') }}
            </x-mailcoach::warning>
        @else
            <p class="text-xs mt-3">{{ __mc("Autosaving every 20 seconds") }}
                - {{ __mc('Last saved at') }} {{ $this->lastSavedAt->toMailcoachFormat() }}</p>
        @endif
    </x-mailcoach::form-buttons>

    @foreach ($contentItems as $index => $contentItem)
        <div class="absolute" wire:key="preview-modal-{{ md5($preview[$contentItem->uuid]) }}">
            <x-mailcoach::preview-modal
                id="preview-{{ $contentItem->uuid }}"
                :html="$preview[$contentItem->uuid]"
                :title="__mc('Preview') . ($contentItem->subject ? ' - ' . $contentItem->subject : '')"
            />
        </div>
    @endforeach
</x-mailcoach::card>
