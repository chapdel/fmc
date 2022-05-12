<div>
    <form
        class="form-grid"
        wire:submit.prevent="save"
        method="POST"
    >
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="template.name" wire:model="template.name" required />

        {!! app(config('mailcoach.campaigns.editor'))->render($template) !!}
    </form>

    <x-mailcoach::preview-modal :title="__('mailcoach - Preview') . ' - ' . $template->name" :html="$template->html" />

    <x-mailcoach::campaign-replacer-help-texts />
</div>
