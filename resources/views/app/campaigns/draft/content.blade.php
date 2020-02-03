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
            @if (config('mailcoach.editor.enabled') && !$campaign->isHtmlCampaign())
                <x-editor-field
                    label="Body (HTML)"
                    name="html"
                    :html="old('html', $campaign->html)"
                    :json="old('json', $campaign->json)"
                    :media-url="route('mailcoach.campaigns.upload', $campaign)"
                ></x-editor-field>
            @else
                <x-html-field label="Body (HTML)" name="html" :value="old('html', $campaign->html)"></x-html-field>
            @endif
        </div>

        <div class="form-buttons">
            <button id="save" type="submit" class="button">
                <x-icon-label icon="fa-code" text="Save content"/>
            </button>

            @unless(config('mailcoach.editor.enabled') && !$campaign->isHtmlCampaign())
                <button type="button" class="link-icon" data-modal-trigger="preview">
                    <x-icon-label icon="fa-eye" text="Preview"/>
                </button>
                <x-modal title="Preview" name="preview" large>
                    <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
                </x-modal>
            @endunless
        </div>

        <x-replacer-help-texts />
    </form>
@endsection
