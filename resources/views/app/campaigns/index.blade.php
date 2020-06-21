@extends('mailcoach::app.layouts.app', ['title' => __('Campaigns')])

@section('header')
<nav>
    <ul class="breadcrumbs">
        <li>
            <span class="breadcrumb">{{ __('Campaigns') }}</span>
        </li>
    </ul>
</nav>
@endsection

@section('content')
<section class="card">
    <div class="table-actions">
        @if ($totalListsCount or $totalCampaignsCount)
            <button class="button" data-modal-trigger="create-campaign">
                <x-icon-label icon="fa-envelope-open" :text="__('Create campaign')" />
            </button>

            <x-modal :title="__('Create campaign')" name="create-campaign" :open="$errors->any()">
                @include('mailcoach::app.campaigns.partials.create')
            </x-modal>
        @endif

        @if($totalCampaignsCount)
            <div class="table-filters">
                <x-filters>
                    <x-filter active-on="" :queryString="$queryString" attribute="status">
                        {{ __('All') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalCampaignsCount) }}</span>
                    </x-filter>
                    <x-filter active-on="sent" :queryString="$queryString" attribute="status">
                        {{ __('Sent') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($sentCampaignsCount) }}</span>
                    </x-filter>
                    <x-filter active-on="scheduled" :queryString="$queryString" attribute="status">
                        {{ __('Scheduled') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($scheduledCampaignsCount) }}</span>
                    </x-filter>
                    <x-filter active-on="draft" :queryString="$queryString" attribute="status">
                        {{ __('Draft') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($draftCampaignsCount) }}</span>
                    </x-filter>
                </x-filters>
                <x-search :placeholder="__('Filter campaigns…')"/>
            </div>
        @endif
    </div>

    @if($totalCampaignsCount)
        <table class="table table-fixed">
            <thead>
                <tr>
                    <x-th class="w-4"></x-th>
                    <x-th sort-by="name">{{ __('Name') }}</x-th>
                    <x-th sort-by="-sent_to_number_of_subscribers" class="w-32 th-numeric">{{ __('Emails') }}</x-th>
                    <x-th sort-by="-unique_open_count" class="w-32 th-numeric hidden | md:table-cell">{{ __('Unique Opens') }}</x-th>
                    <x-th sort-by="-unique_click_count" class="w-32 th-numeric hidden | md:table-cell">{{ __('Unique Clicks') }}</x-th>
                    <x-th sort-by="-sent" sort-default class="w-48 th-numeric hidden | md:table-cell">{{ __('Sent') }}</x-th>
                    <x-th class="w-12"></x-th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                @include('mailcoach::app.campaigns.partials.row')
                @endforeach
            </tbody>
        </table>

        <x-table-status :name="__('campaign|campaigns')" :paginator="$campaigns" :total-count="$totalCampaignsCount"
        :show-all-url="route('mailcoach.campaigns')"></x-table-status>
    @else
        @if ($totalListsCount)
            <p class="alert alert-info">
                {{ __('No campaigns yet. Go write something!') }}
            </p>
        @else
            <p class="alert alert-info">
                {!! __('No campaigns yet, but you‘ll need a list first, go <a href=":emailListsLink">create one</a>!', ['emailListsLink' => route('mailcoach.emailLists')]) !!}
            </p>
        @endif
    @endif
</section>
@endsection
