@extends('mailcoach::app.layouts.app', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $campaign->name
])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href={{ route('mailcoach.campaigns') }}>
                    <span class="breadcrumb">Campaigns</span>
                </a>
            </li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('content')
    <nav class="tabs">
        <ul>
            <c-navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                <c-icon-label icon="fa-chart-area" text="Summary" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.campaigns.opens', $campaign)">
                <c-icon-label icon="fa-envelope-open-text" text="Opens" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)">
                <c-icon-label icon="fa-hand-pointer" text="Clicks" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)">
                <c-icon-label icon="fa-user-slash" text="Unsubscribes" />
            </c-navigation-item>
            <c-navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)">
                <c-icon-label icon="fa-inbox" text="Outbox" />
            </c-navigation-item>
        </ul>
    </nav>

    <section class="card ">
        @yield('campaign')
    </section>
@endsection
