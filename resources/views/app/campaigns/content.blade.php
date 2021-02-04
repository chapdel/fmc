@extends('mailcoach::app.campaigns.layouts.campaign', [
    'campaign' => $campaign,
    'titlePrefix' => __('HTML'),
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}">
            <span class="breadcrumb">{{ $campaign->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">{{ __('Content') }}</span></li>
@endsection

@section('campaign')
    @if ($campaign->isEditable())
        <form
            class="form-grid"
            action="{{ route('mailcoach.campaigns.updateContent', $campaign) }}"
            method="POST"
            data-dirty-check
        >
            @csrf
            @method('PUT')
            {!! app(config('mailcoach.campaigns.editor'))->render($campaign) !!}
        </form>
    @else
        <div>
            <x-mailcoach::html-field :label="__('Body (HTML)')" name="html" :value="$campaign->html" :disabled="! $campaign->isEditable()" />
        </div>
    @endif

    <x-mailcoach::replacer-help-texts />
@endsection
