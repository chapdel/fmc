{{ __('mailcoach - Hey') }},

{{ __('mailcoach - You are almost subscribed to the list **:emailListName**.', ['emailListName'=>$subscriber->emailList->name]) }}

{{ __('mailcoach - Prove it is really you by pressing the button below') }}.

<a href="{{ $confirmationUrl }}">
    {{ __('mailcoach - Confirm subscription') }}
</a>

{{ $confirmationUrl }}

