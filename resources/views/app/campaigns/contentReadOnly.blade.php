<x-mailcoach::layout-campaign :title="__mc('Content')" :campaign="$campaign">
    <div class="card-grid">
        <x-mailcoach::card>
            <x-mailcoach::html-field :label="__mc('Body (HTML)')" name="html" :value="$campaign->webview_html ?? $campaign->html" :disabled="! $campaign->isEditable()" />
        </x-mailcoach::card>

        <x-mailcoach::card>
            <x-mailcoach::web-view src="{{ $campaign->webviewUrl() }}"/>
        </x-mailcoach::card>
    </div>
</x-mailcoach::layout-campaign>
