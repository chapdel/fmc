@extends('mailcoach::app.layouts.app', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $emailList->name
])



@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href="{{ route('mailcoach.emailLists') }}">
                    <span class="breadcrumb">Lists</span>
                </a>
            </li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('content')
    <nav class="tabs">
        <ul>
            <c-navigation-item :href="route('mailcoach.emailLists.subscribers', $emailList)">
                <c-icon-label icon="fa-users" text="Subscribers" :count="$emailList->subscribers()->count() ?? 0" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.emailLists.tags', $emailList)">
                <c-icon-label icon="fa-tag" text="Tags" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.emailLists.segments', $emailList)">
                <c-icon-label icon="fa-chart-pie" text="Segments" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.emailLists.settings', $emailList)">
                <c-icon-label icon="fa-cog" text="Settings" />
            </c-navigation-item>
        </ul>
    </nav>

    <section class="card ">
        @yield('emailList')
    </section>
@endsection
