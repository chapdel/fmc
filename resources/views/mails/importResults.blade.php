@component('mail::message')
Good news!

Your import was processed.

**{{ $subscriberImport->imported_subscribers_count }}** {{ \Illuminate\Support\Str::plural('subscriber', $subscriberImport->imported_subscribers_count) }} have been subscribed to the list {{ $subscriberImport->emailList->name }}.

There {{ $subscriberImport->error_count === 1 ? 'was' : 'were' }} {{ $subscriberImport->error_count }} {{ \Illuminate\Support\Str::plural('error', $subscriberImport->error_count) }}.

More details:

@if($subscriberImport->error_count)
- <a href="{{ $subscriberImport->getErrorReportUrl() }}">Error Report</a>
@endif
- <a href="{{ $subscriberImport->getImportedSubscribersReportUrl() }}">Imported subscribers</a>
- <a href="{{ $subscriberImport->getImportFileUrl() }}">Original import upload</a>

@component('mail::button', ['url' => $subscriberImport->emailList->url])
    View list
@endcomponent

@endcomponent
