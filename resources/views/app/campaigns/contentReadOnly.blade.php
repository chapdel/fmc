<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">
        <div>
            <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="$campaign->html" :disabled="! $campaign->isEditable()" />
        </div>

        <x-mailcoach::web-view src="{{ $campaign->webviewUrl() }}"/>

</x-mailcoach::layout-campaign>
