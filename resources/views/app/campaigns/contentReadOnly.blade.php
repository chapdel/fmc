<x-mailcoach::layout-campaign :title="__('Content')" :campaign="$campaign">
        <div>
            <x-mailcoach::html-field :label="__('Body (HTML)')" name="html" :value="$campaign->html" :disabled="! $campaign->isEditable()" />
        </div>
</x-mailcoach::layout-campaign>
