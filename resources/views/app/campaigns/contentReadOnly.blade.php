<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">
        <div>
            <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="$campaign->html" :disabled="! $campaign->isEditable()" />
        </div>

    <div class="border border-gray-100 rounded p-2 mt-4">
        <x-mailcoach::web-view src="{{ $campaign->webviewUrl() }}"/>
    </div>
</x-mailcoach::layout-campaign>
