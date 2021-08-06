<x-mailcoach::layout-main
    :title="$template->name"
    :originTitle="__('Templates')"
    :originHref="route('mailcoach.templates')"
>
    <form
        class="form-grid"
        action="{{ route('mailcoach.templates.edit', $template) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('Name')" name="name" :value="$template->name" required />

        {!! app(config('mailcoach.campaigns.editor'))->render($template) !!}
    </form>
    
    <x-mailcoach::modal :title="__('Preview') . ' - ' . $campaign->subject" name="preview" large :open="Request::get('modal')">
        <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
    </x-mailcoach::modal>

    <x-mailcoach::modal :title="__('Send Test')" name="send-test">
        @include('mailcoach::app.campaigns.partials.test')
    </x-mailcoach::modal>

    <x-mailcoach::campaign-replacer-help-texts />
</x-mailcoach::layout-main>
