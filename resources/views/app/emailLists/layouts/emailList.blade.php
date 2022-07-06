<x-mailcoach::layout
    :originTitle="$originTitle ?? $emailList->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
    :hideCard="isset($hideCard) ? true : false"
>

    <x-slot name="nav">
        <x-mailcoach::navigation :title="$emailList->name">
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.summary', $emailList)">
                {{__('mailcoach - Performance')}}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscribers', $emailList)">
                <span class="flex items-center">
                    {{ __('mailcoach - Subscribers')}}
                    <span class="counter mx-2">{{ number_format($emailList->subscribers()->count() ?? 0) }}</span>
                </span>
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.tags.*')" :href="route('mailcoach.emailLists.tags', $emailList) . '?type=default'">
                {{ __('mailcoach - Tags') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.segments.*')" :href="route('mailcoach.emailLists.segments', $emailList)">
                {{ __('mailcoach - Segments') }}
            </x-mailcoach::navigation-item>

            <x-mailcoach::navigation-group icon="fas fa-cog" :title="__('mailcoach - Settings')">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.general-settings', $emailList)">
                    {{ __('mailcoach - General') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.onboarding', $emailList)">
                    {{ __('mailcoach - Onboarding') }}
                </x-mailcoach::navigation-item>
                @if(count(config('mail.mailers')) > 1)
                    <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.mailers', $emailList)">
                        {{ __('mailcoach - Mailers') }}
                    </x-mailcoach::navigation-item>
                @endif
            </x-mailcoach::navigation-group>

            @include('mailcoach::app.emailLists.layouts.partials.afterLastTab')
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
