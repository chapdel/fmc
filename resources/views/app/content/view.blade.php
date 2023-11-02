<div class="card-grid" x-data="{ show: 'content' }" x-cloak>
    <nav class="tabs mb-0">
        <ul>
            <x-mailcoach::navigation-item @click.prevent="show = 'content'" x-bind:class="show === 'content' ? 'navigation-item-active' : ''">
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item @click.prevent="show = 'html'" x-bind:class="show === 'html' ? 'navigation-item-active' : ''">
                {{ __mc('HTML') }}
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    <div class="grid gap-6 grid-cols-{{ $model->contentItems->count() > 1 ? '2' : '1' }}">
        @foreach ($model->contentItems as $contentItem)
            <div class="flex flex-col gap-y-4">
                <x-mailcoach::card>
                    <x-mailcoach::text-field :label="__mc('Subject')" name="subject" :value="$contentItem->subject" :disabled="true" />
                </x-mailcoach::card>

                <x-mailcoach::card x-show="show === 'content'">
                    <x-mailcoach::web-view :html="$contentItem->webview_html ?? $contentItem->email_html" />
                </x-mailcoach::card>

                <x-mailcoach::card x-show="show === 'html'">
                    <x-mailcoach::html-field :label="__mc('Body (HTML)')" name="html" :value="$contentItem->webview_html ?? $contentItem->html" :disabled="! $model->isEditable()" />
                </x-mailcoach::card>
            </div>
        @endforeach
    </div>
</div>
