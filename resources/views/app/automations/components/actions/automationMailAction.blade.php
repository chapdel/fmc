<div>
    <x-mailcoach::select-field
        :label="__('Email')"
        name="campaign_id"
        wire:model="campaign_id"
        :placeholder="__('Select an email')"
        :options="['' => 'Select an email'] + $campaignOptions"
    />
</div>
