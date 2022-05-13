<form class="form-grid" wire:submit.prevent="saveCampaign" method="POST">
    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        wire:model.lazy="name"
        name="name"
        :placeholder="__('mailcoach - Newsletter #1')"
        required
    />
    <div class="form-grid">
        <x-mailcoach::select-field
            :label="__('mailcoach - Email list')"
            :options="$emailListOptions"
            wire:model.lazy="email_list_id"
            name="email_list_id"
            required
        />

        @if(count($templateOptions) > 1)
            <x-mailcoach::select-field
                :label="__('mailcoach - Template')"
                :options="$templateOptions"
                wire:model.lazy="template_id"
                name="template_id"
            />
        @endif
    </div>

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create campaign')" />
        <x-mailcoach::button-cancel x-on:click="$store.modals.close('create-campaign')" />
    </div>
</form>
