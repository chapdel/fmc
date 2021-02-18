<x-mailcoach::layout-transactional-template title="Details" :template="$template">
    <form
        class="form-grid"
        method="POST"
    >
        @csrf
        @method('PUT')


        <x-mailcoach::fieldset :legend="__('Recipients')">
            <x-mailcoach::help>
                These recipients will be merged with the ones when the mail is sent. You can specify multiple recipients comma separated
            </x-mailcoach::help>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('To')" name="to" :value="$template->toString()"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('Cc')" name="cc" :value="$template->ccString()"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('Bcc')" name="bcc" :value="$template->bccString()"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::text-field :label="__('Subject')" name="subject" :value="$template->subject" required/>

        {!! app(config('mailcoach.transactional.editor'))->render($template) !!}

    </form>

    <x-mailcoach::transactional-mail-template-replacer-help-texts :template="$template"/>


</x-mailcoach::layout-transactional-template>
