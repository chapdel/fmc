<form action="" class="grid grid-cols-1 gap-6" wire:submit.prevent="sendTest">
    {{-- Start test dialog --}}
    <x-mailcoach::text-field
        :label="__('mailcoach - Test addresses')"
        :placeholder="__('mailcoach - Email(s) comma separated')"
        name="emails"
        :required="true"
        type="text"
        wire:model.lazy="emails"
    />
    <x-mailcoach::button :label="__('mailcoach - Send test')"/>
</form>
