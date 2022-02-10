<x-mailcoach::layout-campaign :title="__('mailcoach - Unsubscribes')" :campaign="$campaign">
    @if($unsubscribes->count())
    <div class="table-actions">
        <div class="table-filters">
            <x-mailcoach::search :placeholder="__('mailcoach - Filter unsubscribes…')" />
        </div>
    </div>

    <table class="table table-fixed">
        <thead>
        <tr>
            <th>{{ __('mailcoach - Email') }}</th>
            <th class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Date') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($unsubscribes as $unsubscribe)
            <tr>
                <td class="markup-links">
                    <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$unsubscribe->subscriber->emailList, $unsubscribe->subscriber]) }}">
                        {{ $unsubscribe->subscriber->email }}
                    </a>
                    <div class="td-secondary-line">
                        {{ $unsubscribe->subscriber->first_name }} {{ $unsubscribe->subscriber->last_name }}
                    </div>
                </td>
                <td class="td-numeric hidden | xl:table-cell">{{ $unsubscribe->created_at->toMailcoachFormat() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <x-mailcoach::table-status
        :name="__('mailcoach - unsubscribe|unsubscribers')"
        :paginator="$unsubscribes"
        :total-count="$totalUnsubscribes"
        :show-all-url="route('mailcoach.campaigns.unsubscribes', $campaign)"
    ></x-mailcoach::table-status>

    @else
        <x-mailcoach::success>
            {{ __('mailcoach - No unsubscribes have been received yet.') }}
        </x-mailcoach::success>
    @endif
</x-mailcoach::layout-campaign>
