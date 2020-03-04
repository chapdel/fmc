@extends('mailcoach::app.layouts.app', ['title' => 'Campaigns'])

@section('header')
<nav>
    <ul class="breadcrumbs">
        <li>
            <span class="breadcrumb">Campaigns</span>
        </li>
    </ul>
</nav>
@endsection

@section('content')
<section class="card">
    <div class="table-actions">
        <button class="button" data-modal-trigger="create-campaign">
            <c-icon-label icon="fa-envelope-open" text="Create campaign" />
        </button>

        <c-modal title="Create campaign" name="create-campaign" :open="$errors->any()">
            @include('mailcoach::app.campaigns.partials.create')
        </c-modal>

        @if($totalCampaignsCount)
            <div class="table-filters">
                <c-filters>
                    <c-context :queryString="$queryString" attribute="status">
                        <c-filter active-on="">
                            All <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalCampaignsCount) }}</span>
                        </c-filter>
                        <c-filter active-on="sent">
                            Sent <span class="counter">{{ Illuminate\Support\Str::shortNumber($sentCampaignsCount) }}</span>
                        </c-filter>
                        <c-filter active-on="scheduled">
                            Scheduled <span class="counter">{{ Illuminate\Support\Str::shortNumber($scheduledCampaignsCount) }}</span>
                        </c-filter>
                        <c-filter active-on="draft">
                            Draft <span class="counter">{{ Illuminate\Support\Str::shortNumber($draftCampaignsCount) }}</span>
                        </c-filter>
                    </c-context>
                </c-filters>
                <c-search placeholder="Filter campaigns…" />
            </div>
        @endif
    </div>

    @if($totalCampaignsCount)
        <table class="table table-fixed">
            <thead>
                <tr>
                    <c-th class="w-4"></c-th>
                    <c-th sort-by="name">Name</c-th>
                    <c-th sort-by="-sent_to_number_of_subscribers" class="w-32 th-numeric">Emails</c-th>
                    <c-th sort-by="-unique_open_count" class="w-32 th-numeric hidden | md:table-cell">Unique opens</c-th>
                    <c-th sort-by="-unique_click_count" class="w-32 th-numeric hidden | md:table-cell">Unique clicks</c-th>
                    <c-th sort-by="-sent" sort-default class="w-48 th-numeric hidden | md:table-cell">Sent</c-th>
                    <c-th class="w-12"></c-th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                @include('mailcoach::app.campaigns.partials.row')
                @endforeach
            </tbody>
        </table>

        <c-table-status name="campaign" :paginator="$campaigns" :total-count="$totalCampaignsCount"
        :show-all-url="route('mailcoach.campaigns')"></c-table-status>
    @else
        <p class="alert alert-info">
            No campaigns yet. Go write something!
        </p>
    @endif
</section>
@endsection
