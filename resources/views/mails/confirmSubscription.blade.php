@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Hey') }},

{{ __('mailcoach - You are almost subscribed to the list **:emailListName**.', ['emailListName'=>$subscriber->emailList->name]) }}

{{ __('mailcoach - Prove it is really you by pressing the button below') }}.

@component('mailcoach::mails.layout.button', ['url' => $confirmationUrl])
{{ __('mailcoach - Confirm subscription') }}
@endcomponent

@endcomponent
