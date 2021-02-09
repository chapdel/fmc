<x-mailcoach::layout-transactional-template :title="Details">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__('Name')" name="name" :value="$template->name" required />
            <x-mailcoach::text-field :label="__('Subject')" name="subject" :value="$template->subject" required />

            {!! app(config('mailcoach.transactional.editor'))->render($template) !!}
        </form>

        <x-mailcoach::replacer-help-texts />
</x-mailcoach::layout-transactional-template>
