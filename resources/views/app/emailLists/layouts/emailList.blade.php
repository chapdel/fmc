<x-mailcoach::layout 
    :originTitle="$originTitle ?? $emailList->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$emailList->name" :backHref="route('mailcoach.emailLists')" :backLabel="__('Lists')">
            <x-mailcoach::navigation-group icon="far fa-magic" :title="__('List')">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.summary', $emailList)">
                    {{__('Performance')}}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscribers', $emailList)">
                    <span class="counter mr-2">{{ number_format($emailList->subscribers()->count() ?? 0) }}</span>
                    {{ __('Subscribers')}} 
                </x-mailcoach::navigation-item>
        
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.tags', $emailList)">
                    {{ __('Tags') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.segments', $emailList)">
                    {{ __('Segments') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>
            
            <x-mailcoach::navigation-group icon="far fa-cog" :title="__('Settings')">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.settings', $emailList)">
                    {{ __('General') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.settings', $emailList)">
                    {{ __('Onboarding') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.settings', $emailList)">
                    {{ __('Mailers') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>
            
            @include('mailcoach::app.emailLists.layouts.partials.afterLastTab')
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
