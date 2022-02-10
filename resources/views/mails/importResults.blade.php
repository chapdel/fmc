@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Good news!') }}

{{ __('mailcoach - Your import was processed') }}.

{{ __('mailcoach - **:count** :subscriber have been subscribed to the list :emailListName',['count'=>$subscriberImport->imported_subscribers_count,'emailListName'=>$subscriberImport->emailList->name,'subscriber'=>trans_choice(__('mailcoach - subscriber|subscribers'),$subscriberImport->imported_subscribers_count)]) }}.

{{ trans_choice(__('mailcoach - There was 1 error.|There were :count errors.'), $subscriberImport->error_count) }}

@component('mailcoach::mails.layout.button', ['url' => $subscriberImport->emailList->url])
{{ __('mailcoach - View list') }}
@endcomponent

@endcomponent
