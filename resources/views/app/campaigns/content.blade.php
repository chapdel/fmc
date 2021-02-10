<x-mailcoach::layout-campaign :title="__('Content')" :campaign="$campaign">
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

    <x-mailcoach::campaign-replacer-help-texts />
</x-mailcoach::layout-campaign>
