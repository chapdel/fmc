@component('mail::message')
Hi,

Here's what's been happening last week at your list **{{ $emailList->name }}** since {{ $summaryStartDateTime->toMailcoachFormat() }}.

@component('mail::panel')
- New subscriptions: <strong>{{ $summary['total_number_of_subscribers_gained'] }}</strong>
- Unsubscribes: <strong>{{ $summary['total_number_of_unsubscribes_gained'] }}</strong>
- Total number of subscribers: <strong>{{ $summary['total_number_of_subscribers'] }}</strong>
@endcomponent

@component('mail::button', ['url' => $emailListUrl])
    View list
@endcomponent

@endcomponent
