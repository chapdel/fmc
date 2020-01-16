@extends('mailcoach::app.campaigns.draft.layouts.edit', [
    'campaign' => $campaign,
    'titlePrefix' => 'HTML',
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}">
            <span class="breadcrumb">{{ $campaign->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">Content</span></li>
@endsection

@section('campaign')
    <form
        class="form-grid"
        action="{{ route('mailcoach.campaigns.updateContent', $campaign) }}"
        method="POST"
        data-dirty-check
    >
        @csrf
        @method('PUT')

        <div>
            @include('mailcoach::app.campaigns.draft.partials.htmlField')
        </div>

        <div class="form-buttons">
            <button type="submit" class="button">
                <x-icon-label icon="fa-code" text="Save content"/>
            </button>

            <button type="button" class="link-icon" data-modal-trigger="preview">
                <x-icon-label icon="fa-eye" text="Preview"/>
            </button>
            <x-modal title="Preview" name="preview" large>
                <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
            </x-modal>
        </div>

        <x-replacer-help-texts />
    </form>
@endsection
