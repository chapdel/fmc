@php($emailList = $getRecord())

<div class="link fi-ta-text-item inline-flex items-center gap-1.5 px-3">
    {{ $emailList->name }}
    @if ($emailList->websiteEnabled())
        <a class="link text-xs" x-data x-tooltip="'{{ __mc('View website') }}'" title="{{ __mc('Website') }}" href="{{ $emailList->websiteUrl() }}" target="_blank">
            <i class="fas fa-external-link"></i>
        </a>
    @endif
</div>
