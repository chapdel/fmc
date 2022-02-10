<x-mailcoach::layout-campaign :title="__('mailcoach - Outbox')" :campaign="$campaign">
    @if ($totalFailed > 0)
        <div class="table-actions">
            <x-mailcoach::form-button
            :action="route('mailcoach.campaigns.retry-failed-sends', [$campaign])"
            method="POST"
            data-confirm="true"
            :data-confirm-text="__('mailcoach - Are you sure you want to resend :totalFailed mails?', ['totalFailed' => $totalFailed])"
            class="mt-4 button"
            >
                {{ __('mailcoach - Try resending :totalFailed :email', ['totalFailed' => $totalFailed, 'email' => trans_choice(__('mailcoach - email|emails'), $totalFailed)]) }}
            </x-mailcoach::form-button>
    </div>
    @endif

    <div class="table-actions">
        <div class="table-filters">
            <x-mailcoach::filters>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="">
                    {{ __('mailcoach - All') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSends) }}</span>
                </x-mailcoach::filter>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="pending">
                    {{ __('mailcoach - Pending') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalPending) }}</span>
                </x-mailcoach::filter>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="failed">
                    {{ __('mailcoach - Failed') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalFailed) }}</span>
                </x-mailcoach::filter>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="sent">
                    {{ __('mailcoach - Sent') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalSent) }}</span>
                </x-mailcoach::filter>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="bounced">
                    {{ __('mailcoach - Bounced') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalBounces) }}</span>
                </x-mailcoach::filter>
                <x-mailcoach::filter :queryString="$queryString" attribute="type" active-on="complained">
                    {{ __('mailcoach - Complaints') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalComplaints) }}</span>
                </x-mailcoach::filter>
            </x-mailcoach::filters>

            <x-mailcoach::search :placeholder="__('mailcoach - Filter mails…')"/>
        </div>
    </div>

    <table class="table table-fixed">
        <thead>
        <tr>
            <x-mailcoach::th sort-by="subscriber_email">{{ __('mailcoach - Email address') }}</x-mailcoach::th>
            <x-mailcoach::th sort-by="subscriber_email">{{ __('mailcoach - Problem') }}</x-mailcoach::th>
            <x-mailcoach::th class="w-48 th-numeric hidden | xl:table-cell" sort-by="-sent_at" sort-default>{{ __('mailcoach - Sent at') }}</x-mailcoach::th>
        </tr>
        </thead>
        <tbody>
        @foreach($sends as $send)
            <tr class="markup-links">
                <td>
                    @if ($send->subscriber)
                        <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$send->subscriber->emailList, $send->subscriber]) }}">{{ $send->subscriber->email }}</a>
                    @else
                        &lt;{{ __('mailcoach - deleted subscriber') }}&gt;
                    @endif
                </td>
                <td>{{ $send->failure_reason }}{{optional($send->latestFeedback())->formatted_type }}</td>
                <td class="td-numeric hidden | xl:table-cell">{{ optional($send->sent_at)->toMailcoachFormat() ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <x-mailcoach::table-status :name="__('mailcoach - send|sends')" :paginator="$sends" :total-count="$totalSends"
                    :show-all-url="route('mailcoach.campaigns.outbox', $campaign)"></x-mailcoach::table-status>
</x-mailcoach::layout-campaign>
