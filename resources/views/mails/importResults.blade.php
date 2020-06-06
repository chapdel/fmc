@component('mail::message')
{{ __('Good news!') }}

{{ __('Your import was processed') }}.

{{ __('**:count** :subscriber have been subscribed to the list :emailListName',['count'=>$subscriberImport->imported_subscribers_count,'emailListName'=>$subscriberImport->emailList->name,'subscriber'=>(\Illuminate\Support\Str::plural('subscriber', $subscriberImport->imported_subscribers_count) )]) }}.

{{ trans_choice(__('There was 1 error.|There were :count errors.'), $subscriberImport->error_count) }}

@component('mail::button', ['url' => $subscriberImport->emailList->url])
    {{ __('View list') }}
@endcomponent

@endcomponent
