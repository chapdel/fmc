@props([
    'previewHtml' => '',
    'model' => null,
])
<x-mailcoach::form-buttons>
    <x-mailcoach::button
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
        wire:click.prevent="save"
        :label="__('mailcoach - Save content')"
    />

    @if (method_exists($model, 'sendTestMail'))
        <x-mailcoach::button x-on:click.prevent="$wire.save() && $store.modals.open('send-test')" class="ml-2" :label="__('mailcoach - Save and send test')"/>
        <x-mailcoach::modal name="send-test">
            <livewire:mailcoach::send-test :model="$model" />
        </x-mailcoach::modal>
    @endif

    <x-mailcoach::button-secondary x-on:click.prevent="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
    {{ $slot }}

    <x-mailcoach::preview-modal name="preview" :html="$previewHtml" :title="__('mailcoach - Preview') . ($model->subject ? ' - ' . $model->subject : '')" />
</x-mailcoach::form-buttons>
