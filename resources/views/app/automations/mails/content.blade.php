<x-mailcoach::layout-automation-mail :title="__('mailcoach - Content')" :mail="$mail">
    @livewire(\Livewire\Livewire::getAlias(config('mailcoach.content_editor')), [
        'model' => $mail,
    ])
</x-mailcoach::layout-automation-mail>
