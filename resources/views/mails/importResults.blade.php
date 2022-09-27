@component('mailcoach::mails.layout.message')
{{ __mc('Good news!') }}

{{ __mc('Your import was processed') }}.

{{ __mc('**:count** :subscriber have been added to the list :emailListName',['count'=>$subscriberImport->imported_subscribers_count,'emailListName'=>$subscriberImport->emailList->name,'subscriber'=>trans_choice('mailcoach - subscriber|subscribers',$subscriberImport->imported_subscribers_count)]) }}.

@if ($subscriberImport->errors)
{{ trans_choice('mailcoach - There was 1 error.|There were :count errors.', count($subscriberImport->errors ?? [])) }}
@endif

@component('mailcoach::mails.layout.button', ['url' => action([\Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\SubscriberImportsController::class, 'index'], $subscriberImport->emailList)])
{{ __mc('View list') }}
@endcomponent

@endcomponent
