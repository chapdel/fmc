<form
    action="{{ route('mailcoach.automations.mails.sendTestEmail', $mail) }}"
    method="POST"

>
    @csrf

    <div class="flex items-end">
        <div class="flex-grow max-w-xl">
            <x-mailcoach::text-field
                :label="__mc('Test addresses')"
                :placeholder="__mc('Email(s) comma separated')"
                name="emails"
                :required="true"
                type="text"
                :value="cache()->get('mailcoach-test-email-addresses')"
            />
        </div>

        <x-mailcoach::button class="ml-2" :label="__mc('Send Test')"/>
    </div>
</form>
