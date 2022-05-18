<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">

    @livewire(Livewire::getAlias(config('mailcoach.content_editor')), [
        'model' => $campaign,
    ])

</x-mailcoach::layout-campaign>
