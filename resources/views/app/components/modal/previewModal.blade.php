@props([
    'html' => '',
    'name' => 'preview',
    'title' => 'Preview',
])
<div>
    <input type="hidden" id="preview-content" value="{{ base64_encode($html) }}">

    <x-mailcoach::modal
        x-effect="
            const open = $store.modals.isOpen('{{ $name }}');
            if (! document.getElementById('{{ $name }}-iframe')) return;
            document.getElementById('{{ $name }}-iframe').src = 'data:text/html;base64,' + document.getElementById('preview-content').value;
        "
        :title="$title"
        :name="$name"
        large
        :open="request()->get('modal') === $name"
        :dismissable="true"
    >
        <iframe style="width: 100%; height: 100%;" id="{{ $name }}-iframe"></iframe>
    </x-mailcoach::modal>
</div>
