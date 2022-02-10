@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Hi') }},

{{ __('mailcoach - You are now subscribed to list :emailListName', ['emailListName'=>$subscriber->emailList->name]) }}.

{{ __('mailcoach - Happy to have you!') }}!

@slot('subcopy')
{!! __('mailcoach - If you accidentally subscribed to this list, click here to <a href=":unsubscribelink">unsubscribe</a>',['unsubscribelink'=>$subscriber->unsubscribeUrl()]) !!}
@endslot

@endcomponent
