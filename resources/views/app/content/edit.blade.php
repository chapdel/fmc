<x-mailcoach::card class="flex flex-col gap-y-4 {{ $contentItems->count() > 1 ? 'p-6' : '' }}">
    @foreach ($contentItems as $index => $contentItem)
        <div
            @if ($contentItems->count() > 1) class="border border-indigo-700/10 rounded bg-indigo-200/10 p-6 mb-6" @endif
            wire:key="{{ $contentItem->uuid }}"
            x-data="{
                collapsed: false,
            }"
        >
            @if ($contentItems->count() > 1)
                <div class="flex items-center" x-bind:class="collapsed ? '' : 'mb-6'" x-cloak>
                    <div class="flex items-center gap-x-2">
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
                    <div class="ml-auto flex items-center gap-x-4">
                        <button type="button" x-tooltip="'{{ __mc('Add split test') }}'" wire:click="addSplitTest('{{ $contentItem->uuid }}')">
                            <x-icon class="w-5 h-5" name="heroicon-o-document-plus" />
                        </button>
                        <button type="button" x-tooltip="'{{ __mc('Preview') }}'" x-on:click.prevent="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })">
                            <x-icon class="w-5 h-5" name="heroicon-o-eye" />
                        </button>
                        <x-mailcoach::preview-modal
                            id="preview-{{ $contentItem->uuid }}"
                            :html="$contentItem->getHtml()"
                            :title="__mc('Preview') . ($contentItem->subject ? ' - ' . $contentItem->subject : '')"/>
                        <x-mailcoach::confirm-button on-confirm="() => $wire.deleteSplitTest('{{ $contentItem->uuid }}')" confirm-text="{{ __mc('Are you sure you want to delete this split test?') }}" x-tooltip="'{{ __mc('Delete') }}'">
                            <x-icon class="w-5 h-5 link-danger" name="heroicon-o-trash" />
                        </x-mailcoach::confirm-button>
                    </div>
                </div>
            @endif

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
        <x-mailcoach::editor-buttons :preview-html="$contentItem->getHtml()" :show-preview="$contentItems->count() === 1" :model="$contentItem">
            @if ($canBeSplitTested)
                <x-mailcoach::button-secondary wire:click.prevent="addSplitTest" :label="__mc('Add split test')" />
            @endif
        </x-mailcoach::editor-buttons>
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
