<x-mailcoach::layout-segment
    :segment="$segment"
    :selectedSubscribersCount="$selectedSubscribersCount"
>
    @if($selectedSubscribersCount)

        @if($subscribersCount = $segment->emailList->subscribers()->count())
            <div class="alert alert-info mb-8">
                {!! __('mailcoach - Population is <strong>:percentage%</strong> of list total of :subscribersCount.', ['percentage' => round($selectedSubscribersCount / $subscribersCount * 100 , 2), 'subscribersCount' => number_format($subscribersCount)]) !!}
            </div>
        @endif

        <div class="table-overflow">
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-mailcoach::th sort-by="email">{{ __('mailcoach - Email') }}</x-mailcoach::th>
                    <th>{{ __('mailcoach - Tags') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subscribers as $subscriber)
                    <tr class="markup-links">
                        <td>
                            <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$emailList, $subscriber]) }}">
                                {{ $subscriber->email }}
                            </a>
                            <div class="td-secondary-line">
                                {{ $subscriber->first_name }} {{ $subscriber->last_name }}
                            </div>
                        </td>
                        <td>
                            @foreach($subscriber->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT) as $tag)
                                @include('mailcoach::app.partials.tag')
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <x-mailcoach::table-status :name="__('mailcoach - subscriber|subscribers')" :paginator="$subscribers" :total-count="$selectedSubscribersCount"
                        :show-all-url="route('mailcoach.emailLists.segment.subscribers', [$segment->emailList, $segment])">
        </x-mailcoach::table-status>
    @else
        <x-mailcoach::help>
            {{ __('mailcoach - This is a very exclusive segment. Nobody got selected.') }}
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-segment>
