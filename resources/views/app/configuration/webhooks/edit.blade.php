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
                href="https://mailcoach.app/docs/cloud/using-mailcoach/webhooks/webhook-payloads" target="_blank">in our docs</a>.
        </x-mailcoach::help>

        <x-mailcoach::text-field :label="__mc('Name')" name="webhook.name" wire:model.lazy="webhook.name" required />

        <x-mailcoach::text-field :label="__mc('URL')" name="webhook.url" wire:model.lazy="webhook.url" required />

        <div class="flex items-center gap-x-2" x-data="{ type: 'password' }">
            <x-mailcoach::text-field x-bind:type="type" :label="__mc('Secret ')" name="webhook.secret" wire:model.lazy="webhook.secret" required />
            <x-mailcoach::rounded-icon x-on:click="type = type === 'password' ? 'text' : 'password'" class="cursor-pointer mt-6" icon="" x-bind:class="type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash'" size="md" type="info" />
        </div>

        <x-mailcoach::checkbox-field :label="__mc('Use for all lists')" name="webhook.use_for_all_lists" wire:model="webhook.use_for_all_lists" />

        @if (!$webhook->use_for_all_lists)
            <div class="form-field">
                <label class=label>{{__mc('Only for these email lists')}}</label>
                <x-mailcoach::select-field
                    name="emailLists"
                    :multiple="true"
                    wire:model="emailLists"
                    :options="$emailListNames"
                />
            </div>
        @endif

        @if(config('mailcoach.webhooks.selectable_event_types_enabled', false))
            <x-mailcoach::checkbox-field
                name="webhook.use_for_all_events"
                :label="__mc('Use for all events')"
                wire:model="webhook.use_for_all_events"
            />
            @if (!$this->webhook->use_for_all_events)
                <div class="ml-6">
                    @foreach($eventOptions as $event => $name)
                        <div class="mb-4">
                            <x-mailcoach::checkbox-field
                                :name="$event"
                                :value="$event"
                                :label="__mc($name)"
                                wire:model="webhook.events"
                            />
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Save webhook')" />
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>
</form>
