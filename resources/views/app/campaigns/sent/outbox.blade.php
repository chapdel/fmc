@extends('mailcoach::app.campaigns.sent.layouts.show', [
    'campaign' => $campaign,
    'titlePrefix' => __('Outbox'),
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.campaigns.summary', $campaign) }}">
            <span class="breadcrumb">{{ $campaign->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">{{ __('Outbox') }}</span></li>
@endsection

@section('campaign')
    @if ($totalFailed > 0)
        <div class="table-actions">
            <x-form-button
            :action="route('mailcoach.campaigns.retry-failed-sends', [$campaign])"
            method="POST"
            data-confirm="true"
            :data-confirm-text="__('Are you sure you want to resend :totalFailed mails?', ['totalFailed' => $totalFailed])"
            class="mt-4 button"
            >
                <x-icon-label icon="fa-exclamation-triangle" :text="__('Try resending :totalFailed :email', ['totalFailed' => $totalFailed, 'email' => trans_choice(__('email|emails'), $totalFailed)])" />
            </x-form-button>
    </div>
    @endif

    <div class="table-actions">
        <div class="table-filters">
            <x-filters>
                <x-filter :queryString="$queryString" attribute="type" active-on="">
                    {{ __('All') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSends) }}</span>
                </x-filter>
                <x-filter :queryString="$queryString" attribute="type" active-on="pending">
                    {{ __('Pending') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalPending) }}</span>
                </x-filter>
                <x-filter :queryString="$queryString" attribute="type" active-on="failed">
                    {{ __('Failed') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalFailed) }}</span>
                </x-filter>
                <x-filter :queryString="$queryString" attribute="type" active-on="sent">
                    {{ __('Sent') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSent) }}</span>
                </x-filter>
                <x-filter :queryString="$queryString" attribute="type" active-on="bounced">
                    {{ __('Bounced') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalBounces) }}</span>
                </x-filter>
                <x-filter :queryString="$queryString" attribute="type" active-on="complained">
                    {{ __('Complaints') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalComplaints) }}</span>
                </x-filter>
            </x-filters>

            <x-search :placeholder="__('Filter mails…')"/>
        </div>
    </div>

    <table class="table table-fixed">
        <thead>
        <tr>
            <x-th sort-by="subscriber_email">{{ __('Email address') }}</x-th>
            <x-th sort-by="subscriber_email">{{ __('Problem') }}</x-th>
            <x-th class="w-48 th-numeric hidden | md:table-cell" sort-by="-sent_at" sort-default>{{ __('Sent at') }}</x-th>
        </tr>
        </thead>
        <tbody>
        @foreach($sends as $send)
            <tr class="markup-links">
                <td><a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$send->subscriber->emailList, $send->subscriber]) }}">{{ $send->subscriber->email }}</a></td>
                <td>{{ $send->failure_reason }}{{optional($send->latestFeedback())->formatted_type }}</td>
                <td class="td-numeric hidden | md:table-cell">{{ optional($send->sent_at)->toMailcoachFormat() ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <x-table-status :name="__('send|sends')" :paginator="$sends" :total-count="$totalSends"
                    :show-all-url="route('mailcoach.campaigns.outbox', $campaign)"></x-table-status>
@endsection
