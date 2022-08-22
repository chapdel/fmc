@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Good job!') }}

{{ __('mailcoach - Campaign **:campaignName** was sent to **:numberOfSubscribers** subscribers (list :emailListName)',['campaignName'=>$campaign->name,'numberOfSubscribers'=>($campaign->sent_to_number_of_subscribers ?? 0 ),'emailListName'=>$campaign->emailList->name]) }}.

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
{{ __('mailcoach - View summary') }}
@endcomponent

@component('mailcoach::mails.layout.subcopy')
[{{ __('mailcoach - Edit notification settings') }}]({{ $settingsUrl }})
@endcomponent

@endcomponent
