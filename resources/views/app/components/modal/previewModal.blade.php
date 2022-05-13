@props([
    'html' => '',
    'name' => 'preview',
    'title' => 'Preview',
])
<div>
    <input type="hidden" id="preview-content" value="{{ base64_encode($html) }}">

    <x-mailcoach::modal
        x-init="
            Alpine.effect(() => {
                const open = $store.modals.isOpen('preview');
                $refs.iframe.src = 'data:text/html;base64,' + document.getElementById('preview-content').value;
            });
        "
        :title="$title"
        :name="$name"
        large
        :open="request()->get('modal') === $name"
    >
        <iframe width="100%" height="100%" x-ref="iframe"></iframe>
    </x-mailcoach::modal>
</div>
