@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Hey') }},

{{ __('mailcoach - It seems like you haven’t read our emails in a while.') }}

{{ __('mailcoach - Do you want to stay subscribed to our email list **:emailListName**?', ['emailListName'=>$subscriber->emailList->name]) }}

@component('mailcoach::mails.layout.button', ['url' => $confirmationUrl])
{{ __('mailcoach - Stay subscribed') }}
@endcomponent

@endcomponent
