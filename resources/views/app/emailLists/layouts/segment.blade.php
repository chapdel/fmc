@extends('mailcoach::app.layouts.app', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $segment->name
])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href="{{ route('mailcoach.emailLists') }}">
                    <span class="breadcrumb">Lists</span>
                </a>
            </li>
            <li><a href="{{ route('mailcoach.emailLists.subscribers', $segment->emailList) }}"><span class="breadcrumb">{{ $segment->emailList->name }}</span></a></li>
            <li><a href="{{ route('mailcoach.emailLists.segments', $segment->emailList) }}"><span class="breadcrumb">Segments</span></a></li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('content')
    <nav class="tabs">
        <ul>
            <c-navigation-item :href="route('mailcoach.emailLists.segment.edit', [$segment->emailList, $segment])">
                <c-icon-label icon="fa-chart-pie" text="Segment details" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.emailLists.segment.subscribers', [$segment->emailList, $segment])">
                <c-icon-label icon="fa-user" text="Population" :count="$selectedSubscribersCount" />
            </c-navigation-item>
        </ul>
    </nav>

    <section class="card">
        @yield('segment')
    </section>
@endsection
