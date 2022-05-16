<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">

    @livewire(Livewire::getAlias(config('mailcoach.campaigns.editor')), [
        'sendable' => $campaign,
    ])

</x-mailcoach::layout-campaign>
