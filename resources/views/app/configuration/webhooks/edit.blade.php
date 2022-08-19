<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::card>
        <x-mailcoach::help>
            We will send a request to the URL you specified, when one of these events happen:
            <ul>
                <li>A user subscribed</li>
                <li>A user unsubscribed</li>
                <li>A campaign was sent</li>
            </ul>
            You can view more information on the sent payload and security recommendations <a
                href="https://spatie.be/docs/laravel-mailcoach/">in our docs</a>.
        </x-mailcoach::help>

        <x-mailcoach::text-field :label="__('Name')" name="webhook.name" wire:model.lazy="webhook.name" required />

        <x-mailcoach::text-field :label="__('URL')" name="webhook.url" wire:model.lazy="webhook.url" required />

        <x-mailcoach::text-field :label="__('Secret ')" name="webhook.secret" wire:model.lazy="webhook.secret" required />

        <x-mailcoach::checkbox-field :label="__('Use for all lists')" name="webhook.use_for_all_lists" wire:model="webhook.use_for_all_lists" />

        @if (!$webhook->use_for_all_lists)
            <div class="form-field">
                <label class=label>Only for these email lists</label>
                <x-mailcoach::select-field
                    name="email_lists"
                    :multiple="true"
                    wire:model="email_lists"
                    :options="\Spatie\Mailcoach\Domain\Audience\Models\EmailList::get()->pluck('name', 'id')->values()->toArray()"
                />
            </div>
        @endif

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('Save webhook')" />
        </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>
