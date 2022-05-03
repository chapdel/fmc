<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">

    {!! app(config('mailcoach.campaigns.editor'))->render($campaign) !!}

</x-mailcoach::layout-campaign>
