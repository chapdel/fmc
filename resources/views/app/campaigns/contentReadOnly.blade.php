<x-mailcoach::layout-campaign :title="__('mailcoach - Content')" :campaign="$campaign">
    <div class="card-grid">
        <x-mailcoach::card>
            <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="$campaign->html" :disabled="! $campaign->isEditable()" />
        </x-mailcoach::card>

        <x-mailcoach::card>
            <x-mailcoach::web-view src="{{ $campaign->webviewUrl() }}"/>
        </x-mailcoach::card>
    </div>
</x-mailcoach::layout-campaign>
