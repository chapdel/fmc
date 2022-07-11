<x-mailcoach::card>
    <dl class="dl">
        <dt>{{ __('mailcoach - Subject') }}</dt>
        <dd>{{ $transactionalMail->subject }}</dd>

        <x-mailcoach::address-definition :label="__('mailcoach - From')" :addresses="$transactionalMail->from"/>
        <x-mailcoach::address-definition :label="__('mailcoach - To')" :addresses="$transactionalMail->to"/>
        <x-mailcoach::address-definition :label="__('mailcoach - Cc')" :addresses="$transactionalMail->cc"/>
        <x-mailcoach::address-definition :label="__('mailcoach - Bcc')" :addresses="$transactionalMail->bcc"/>

        @if(collect($transactionalMail->attachments)->count() > 0)
            <dt>
                {{ __('mailcoach - Attachments') }}
            </dt>
            <dd>
                <ul class="list-disc list-inside">
                    @foreach(collect($transactionalMail->attachments) as $attachment)
                            <li>
                                {{ $attachment }}
                            </li>
                    @endforeach
                </ul>
            </dd>
        @endif

        <dt class="flex items-start">
            <div>{{ __('mailcoach - Body') }}</div>
        </dt>
        <dd>
            <x-mailcoach::web-view :html="$transactionalMail->body" />
        </dd>
    </dl>
</x-mailcoach::card>
