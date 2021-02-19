@component('mail::message')
{{ __('Hey') }},

{{ __('It seems like you haven’t read our emails in a while.') }}

{{ __('Do you want to stay subscribed to our email list **:emailListName**?', ['emailListName'=>$subscriber->emailList->name]) }}

@component('mail::button', ['url' => $confirmationUrl])
    {{ __('Stay subscribed') }}
@endcomponent

@endcomponent
