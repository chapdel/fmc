@component('mail::message')
Hi,

You are now subscribed to list **{{ $subscriber->emailList->name }}**.

Happy to have you!

@slot('subcopy')
If you accidentally subscribed to this list, click here to <a href="{{ $subscriber->unsubscribeUrl() }}">unsubscribe</a>
@endslot

@endcomponent
