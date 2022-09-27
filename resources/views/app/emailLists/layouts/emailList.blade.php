<x-mailcoach::layout
    :originTitle="$originTitle ?? $emailList->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
    :hideCard="isset($hideCard) ? true : false"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$emailList->name">
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.summary', $emailList)">
                {{__mc('Performance')}}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.subscriber*')" :href="route('mailcoach.emailLists.subscribers', $emailList)">
                <span class="flex items-center">
                    {{ __mc('Subscribers')}}
                    <span class="counter mx-2">{{ number_format($emailList->subscribers()->count() ?? 0) }}</span>
                </span>
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.tags.*')" :href="route('mailcoach.emailLists.tags', $emailList) . '?type=default'">
                {{ __mc('Tags') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.segments.*')" :href="route('mailcoach.emailLists.segments', $emailList)">
                {{ __mc('Segments') }}
            </x-mailcoach::navigation-item>

            <x-mailcoach::navigation-group icon="fas fa-cog" :title="__mc('Settings')" :href="route('mailcoach.emailLists.general-settings', $emailList)">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.general-settings', $emailList)">
                    {{ __mc('General') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.onboarding', $emailList)">
                    {{ __mc('Onboarding') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.mailers', $emailList)">
                    {{ __mc('Mailers') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.website', $emailList)">
                    {{ __mc('Website') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            @include('mailcoach::app.emailLists.layouts.partials.afterLastTab')
        </x-mailcoach::navigation>
    </x-slot>

    @if ($emailList->subscribers()->count() === 0 && !Route::is('mailcoach.emailLists.subscriber*'))
        <x-mailcoach::help class="mb-4">
            {!! __mc('This list is empty. <a href=":url">Add some subscribers</a>', ['url' => route('mailcoach.emailLists.subscribers', $emailList)]) !!}
        </x-mailcoach::help>
    @endif

    {{ $slot }}
</x-mailcoach::layout>
