@extends('mailcoach::app.emailLists.layouts.emailList', [
    'title' => $segment->name,
    'subTitle' => __('Segments')
])

@section('emailList')
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.segment.edit', [$segment->emailList, $segment])">
                {{ __('Segment details') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.segment.subscribers', [$segment->emailList, $segment])">
                <x-mailcoach::icon-label :text="__('Population')" invers :count="$selectedSubscribersCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @yield('segment')
@endsection
