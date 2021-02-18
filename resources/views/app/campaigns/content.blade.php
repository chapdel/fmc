<x-mailcoach::layout-campaign :title="__('Content')" :campaign="$campaign">

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

    <x-mailcoach::campaign-replacer-help-texts />

</x-mailcoach::layout-campaign>
