<x-mailcoach::layout-automation-mail :title="__('Content')" :mail="$mail">

    <form
        class="form-grid"
        action="{{ route('mailcoach.automations.mails.updateContent', $mail) }}"
        method="POST"
        data-dirty-check
    >
        @csrf
        @method('PUT')
        {!! app(config('mailcoach.automation.editor'))->render($mail) !!}
    </form>

    <x-mailcoach::automation-mail-replacer-help-texts />

</x-mailcoach::layout-automation-mail>
