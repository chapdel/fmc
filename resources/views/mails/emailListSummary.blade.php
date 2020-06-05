@component('mail::message')
{{ __('Hi') }},

{{ __('Here\'s what\'s been happening last week at your list **:emaillistname** since :startdate',['emaillistname'=>$emailList->name,'startdate'=>$summaryStartDateTime->toMailcoachFormat()]) }}.

@component('mail::panel')
- {{ __('New subscriptions') }}: <strong>{{ $summary['total_number_of_subscribers_gained'] }}</strong>
- {{ __('Unsubscribes') }}: <strong>{{ $summary['total_number_of_unsubscribes_gained'] }}</strong>
- {{ __('Total number of subscribers') }}: <strong>{{ $summary['total_number_of_subscribers'] }}</strong>
@endcomponent

@component('mail::button', ['url' => $emailListUrl])
    {{ __('View list') }}
@endcomponent

@endcomponent
