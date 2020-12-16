@extends('mailcoach::app.campaigns.layouts.show', ['campaign' => $campaign])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $campaign->name }}</span></li>
@endsection

@section('campaign')
    <form
        class="form-grid"
        action="{{ route('mailcoach.campaigns.settings', $campaign) }}"
        method="POST"
        data-dirty-check
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('Name')" name="name" :value="$campaign->name" required :disabled="! $campaign->isEditable()" />

        <x-mailcoach::text-field :label="__('Subject')" name="subject" :value="$campaign->subject" :disabled="! $campaign->isEditable()" />

        @if (! $campaign->isAutomated())
            @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $campaign])
        @endif

        <div class="form-row">
            <label class="label">{{ __('Track when…') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('Someone opens this email')" name="track_opens" :checked="$campaign->track_opens" />
                <x-mailcoach::checkbox-field :label="__('Links in the email are clicked')" name="track_clicks" :checked="$campaign->track_clicks" />
            </div>
        </div>

        @if ($campaign->isEditable())
            <div class="form-buttons">
                <button type="submit" class="button">
                    <x-mailcoach::icon-label icon="fa-cog" :text="__('Save settings')" />
                </button>
            </div>
        @endif
    </form>
@endsection
