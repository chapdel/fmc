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
            <x-icon-label icon="fa-envelope-open" text="Create campaign" />
        </button>

        <x-modal title="Create campaign" name="create-campaign" :open="$errors->any()">
            @include('mailcoach::app.campaigns.partials.create')
        </x-modal>

        @if($totalCampaignsCount)
            <div class="table-filters">
                <x-filters>
                    <x-context :queryString="$queryString" attribute="status">
                        <x-filter active-on="">
                            All <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalCampaignsCount) }}</span>
                        </x-filter>
                        <x-filter active-on="sent">
                            Sent <span class="counter">{{ Illuminate\Support\Str::shortNumber($sentCampaignsCount) }}</span>
                        </x-filter>
                        <x-filter active-on="scheduled">
                            Scheduled <span class="counter">{{ Illuminate\Support\Str::shortNumber($scheduledCampaignsCount) }}</span>
                        </x-filter>
                        <x-filter active-on="draft">
                            Draft <span class="counter">{{ Illuminate\Support\Str::shortNumber($draftCampaignsCount) }}</span>
                        </x-filter>
                    </x-context>
                </x-filters>
                <x-search placeholder="Filter campaigns…" />
            </div>
        @endif
    </div>

    @if($totalCampaignsCount)
        <table class="table">
            <thead>
                <tr>
                    <x-th class="w-4"></x-th>
                    <x-th sort-by="name">Name</x-th>
                    <x-th sort-by="-sent_to_number_of_subscribers" class="th-numeric">Emails</x-th>
                    <x-th sort-by="-unique_open_count" class="th-numeric hidden | md:table-cell">Unique opens</x-th>
                    <x-th sort-by="-unique_click_count" class="th-numeric hidden | md:table-cell">Unique clicks</x-th>
                    <x-th sort-by="-sent" sort-default class="th-numeric hidden | md:table-cell">Sent</x-th>
                    <x-th></x-th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                @include('mailcoach::app.campaigns.partials.row')
                @endforeach
            </tbody>
        </table>

        <x-table-status name="campaign" :paginator="$campaigns" :total-count="$totalCampaignsCount"
        :show-all-url="route('mailcoach.campaigns')"></x-table-status>
    @else
        <p class="alert alert-info">
            No campaigns yet. Go write something!
        </p>
    @endif
</section>
@endsection
