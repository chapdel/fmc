@extends('mailcoach::app.campaigns.sent.layouts.show', [
    'campaign' => $campaign,
    'titlePrefix' => 'Outbox',
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.campaigns.summary', $campaign) }}">
            <span class="breadcrumb">{{ $campaign->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">Outbox</span></li>
@endsection

@section('campaign')
    @if ($totalFailed > 0)
        <div class="table-actions">
            <c-form-button
            :action="route('mailcoach.campaigns.retry-failed-sends', [$campaign])"
            method="POST"
            data-confirm="true"
            class="mt-4 button"
            >
                <c-icon-label icon="fa-exclamation-triangle" :text="'Try resending ' . $totalFailed . ' failed ' . \Illuminate\Support\Str::plural('email', $totalFailed)" />
            </c-form-button>
    </div>
    @endif

    <div class="table-actions">
        <div class="table-filters">
            <c-filters>
                <c-context :queryString="$queryString" attribute="type">
                    <c-filter active-on="">
                        All <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSends) }}</span>
                    </c-filter>
                    <c-filter active-on="pending">
                        Pending <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalPending) }}</span>
                    </c-filter>
                    <c-filter active-on="failed">
                        Failed <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalFailed) }}</span>
                    </c-filter>
                    <c-filter active-on="sent">
                        Sent <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSent) }}</span>
                    </c-filter>
                    <c-filter active-on="bounced">
                        Bounced <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalBounces) }}</span>
                    </c-filter>
                    <c-filter active-on="complained">
                        Complaints <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalComplaints) }}</span>
                    </c-filter>
                </c-context>
            </c-filters>

            <c-search placeholder="Filter mails…"/>
        </div>
    </div>

    <table class="table table-fixed">
        <thead>
        <tr>
            <c-th sort-by="subscriber_email">Email address</c-th>
            <c-th sort-by="subscriber_email">Problem</c-th>
            <c-th class="w-48 th-numeric hidden | md:table-cell" sort-by="-sent_at" sort-default>Sent at</c-th>
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

    <c-table-status name="send" :paginator="$sends" :total-count="$totalSends"
                    :show-all-url="route('mailcoach.campaigns.outbox', $campaign)"></c-table-status>
@endsection
