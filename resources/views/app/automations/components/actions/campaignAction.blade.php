<div>
    <x-mailcoach::select-field
        label="Campaign"
        name="campaign_id"
        wire:model="campaign_id"
        :placeholder="__('Select a campaign')"
        :options="['' => 'Select a campaign'] + $campaignOptions"
    />
</div>
