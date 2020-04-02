@component('mail::message')
@lang('Good news!')

@lang('Your import was processed').

@lang('**:count** :subscriber have been subscribed to the list :emaillistname',['count'=>$subscriberImport->imported_subscribers_count,'emaillistname'=>$subscriberImport->emailList->name,'subscriber'=>(\Illuminate\Support\Str::plural('subscriber', $subscriberImport->imported_subscribers_count) )]).

@lang('')There {{ $subscriberImport->error_count === 1 ? 'was' : 'were' }} {{ $subscriberImport->error_count }} {{ \Illuminate\Support\Str::plural('error', $subscriberImport->error_count) }}.

@lang('More details'):

@if($subscriberImport->error_count)
- <a href="{{ $subscriberImport->getErrorReportUrl() }}">@lang('Error Report')</a>
@endif
- <a href="{{ $subscriberImport->getImportedSubscribersReportUrl() }}">@lang('Imported subscribers')</a>
- <a href="{{ $subscriberImport->getImportFileUrl() }}">@lang('Original import upload')</a>

@component('mail::button', ['url' => $subscriberImport->emailList->url])
    @lang('View list')
@endcomponent

@endcomponent
