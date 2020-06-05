@component('mail::message')
{{ __('Good news!') }}

{{ __('Your import was processed') }}.

{{ __('**:count** :subscriber have been subscribed to the list :emaillistname',['count'=>$subscriberImport->imported_subscribers_count,'emaillistname'=>$subscriberImport->emailList->name,'subscriber'=>(\Illuminate\Support\Str::plural('subscriber', $subscriberImport->imported_subscribers_count) )]) }}.

There {{ $subscriberImport->error_count === 1 ? 'was' : 'were' }} {{ $subscriberImport->error_count }} {{ \Illuminate\Support\Str::plural('error', $subscriberImport->error_count) }}.

@component('mail::button', ['url' => $subscriberImport->emailList->url])
    {{ __('View list') }}
@endcomponent

@endcomponent
