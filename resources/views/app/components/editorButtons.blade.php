@props([
    'previewHtml' => '',
    'model' => null,
    'showPreview' => true,
])
@if($model instanceof \Spatie\Mailcoach\Domain\Shared\Models\Sendable)
    @pushonce('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            setInterval(() => @this.autosave(), 20000
        )
            ;
        });
    </script>
    @endpushonce
@endif
<x-mailcoach::form-buttons>
    <div class="flex gap-x-2">
        <x-mailcoach::button
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            wire:click.prevent="save"
            :label="__mc('Save content')"
        />

        @if ($showPreview && config('mailcoach.content_editor') !== \Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class)
            <x-mailcoach::button-secondary x-on:click.prevent="$dispatch('open-modal', { id: 'preview' })"
                                           :label="__mc('Preview')"/>
            <x-mailcoach::preview-modal name="preview" :html="$previewHtml"
                                        :title="__mc('Preview') . ($model->subject ? ' - ' . $model->subject : '')"/>
        @endif

        {{ $slot }}
    </div>

    @if ($model instanceof \Spatie\Mailcoach\Domain\Template\Models\Template && ! preg_match_all('/\[\[\[(.*?)\]\]\]/', $previewHtml, $matches))
        <x-mailcoach::info class="mt-6">
            {!! __mc('We found no slots in this template. You can add slots by adding the name in triple brackets, for example: <code>[[[content]]]</code>.') !!}
        </x-mailcoach::info>
    @endif

    @if ($model instanceof \Spatie\Mailcoach\Domain\Shared\Models\Sendable)
        @if ($this->autosaveConflict)
            <x-mailcoach::warning class="mt-4">
                {{ __mc('Autosave disabled, the content was saved somewhere else. Refresh the page to get the latest content or save manually to override.') }}
            </x-mailcoach::warning>
        @else
            <p class="text-xs mt-3">{{ __mc("We autosave every 20 seconds") }}
                - {{ __mc('Last saved at') }} {{ $model->updated_at->toMailcoachFormat() }}</p>
        @endif
    @endif
</x-mailcoach::form-buttons>
