@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Hi') }},

{{ __("mailcoach - Here's what's been happening last week at your list **:emailListName** since :startDate", ['emailListName'=>$emailList->name, 'startDate'=>$summaryStartDateTime->toMailcoachFormat()]) }}.

@component('mailcoach::mails.layout.panel')
- {{ __('mailcoach - New subscriptions') }}: <strong>{{ number_format($summary['total_number_of_subscribers_gained']) }}</strong>
- {{ __('mailcoach - Unsubscribes') }}: <strong>{{ number_format($summary['total_number_of_unsubscribes_gained']) }}</strong>
- {{ __('mailcoach - Total number of subscribers') }}: <strong>{{ number_format($summary['total_number_of_subscribers']) }}</strong>
@endcomponent

@component('mailcoach::mails.layout.button', ['url' => $emailListUrl])
{{ __('mailcoach - View list') }}
@endcomponent

@endcomponent
