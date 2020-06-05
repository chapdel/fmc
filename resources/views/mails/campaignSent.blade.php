@component('mailcoach::mails.layout.message')
{{ __('Good job!') }}

{{ __('Campaign **:campaignname** was sent to **:number_of_subscribers** subscribers (list :emaillistname)',['campaignname'=>$campaign->name,'number_of_subscribers'=>($campaign->sent_to_number_of_subscribers ?? 0 ),'emaillistname'=>$campaign->emailList->name]) }}.

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
    {{ __('View summary') }}
@endcomponent

@endcomponent
