<x-mailcoach::layout-transactional
    :title="__('Content')"
    :transactionalMail="$transactionalMail"
>

    Subject: {{ $transactionalMail->subject }}<br/>

    <x-mailcoach::mail-persons label="From" :persons="$transactionalMail->from"/>
    <x-mailcoach::mail-persons label="To" :persons="$transactionalMail->to"/>
    <x-mailcoach::mail-persons label="Cc" :persons="$transactionalMail->cc"/>
    <x-mailcoach::mail-persons label="Bcc" :persons="$transactionalMail->bcc"/>

    <h3>Opens</h3>
    <ul>
        @forelse($transactionalMail->opens as $open)
            <li>{{ $open->created_at }}</li>
        @empty
            This mail hasn't been opened yet.
        @endforelse
    </ul>

    <x-mailcoach::form-button action="{{ route('mailcoach.transactionalMail.resend', $transactionalMail) }}">
        Resend
    </x-mailcoach::form-button>

    <iframe width="560" height="315" src="{{ route('mailcoach.transactionalMail.body', $transactionalMail) }}"/>
</x-mailcoach::layout-transactional>
