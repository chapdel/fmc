@component('mailcoach::mails.layout.message')
Good job!

Campaign **{{ $campaign->name }}** was sent to **{{ $campaign->sent_to_number_of_subscribers ?? 0  }}** subscribers (list {{ $campaign->emailList->name  }}).

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
    View summary
@endcomponent

@endcomponent
