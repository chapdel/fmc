<x-mailcoach::layout-list
    :title="$segment->name"
    :originTitle="__('mailcoach - Segments')"
    :originHref="route('mailcoach.emailLists.segments', ['emailList' => $segment->emailList])"
    :emailList="$segment->emailList"
>
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.segment.edit', [$segment->emailList, $segment])">
                {{ __('mailcoach - Segment details') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.segment.subscribers', [$segment->emailList, $segment])">
                <x-mailcoach::icon-label :text="__('mailcoach - Population')" invers :count="$selectedSubscribersCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    {{ $slot }}
</x-mailcoach::layout-list>
