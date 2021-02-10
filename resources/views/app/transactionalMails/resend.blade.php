<x-mailcoach::layout-transactional
    :title="__('Resent')"
    :transactionalMail="$transactionalMail"
>

    <x-mailcoach::form-button action="{{ route('mailcoach.transactionalMail.resend', $transactionalMail) }}">
        Resend
    </x-mailcoach::form-button>
</x-mailcoach::layout-transactional>
