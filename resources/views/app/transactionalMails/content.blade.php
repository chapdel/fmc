<dl class="dl contents-start">
    <dt>Subject</dt>
    <dd>{{ $transactionalMail->subject }}</dd>

    <x-mailcoach::address-definition label="From" :addresses="$transactionalMail->from"/>
    <x-mailcoach::address-definition label="To" :addresses="$transactionalMail->to"/>
    <x-mailcoach::address-definition label="Cc" :addresses="$transactionalMail->cc"/>
    <x-mailcoach::address-definition label="Bcc" :addresses="$transactionalMail->bcc"/>

    <dt class="flex items-start">
        <div>{{ __('mailcoach - Body') }}</div>
    </dt>
    <dd>
        <x-mailcoach::web-view :html="$transactionalMail->body" />
    </dd>
</dl>
