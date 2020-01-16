@component('mail::message')
Hey,

You are almost subscribed to the list **{{ $subscriber->emailList->name }}**.

Prove it is really you by pressing the button below.

@component('mail::button', ['url' => $confirmationUrl])
    Confirm subscription
@endcomponent

@endcomponent
