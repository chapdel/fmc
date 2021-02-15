<div>
    <x-mailcoach::select-field
        label="Mail"
        name="automation_mail_id"
        wire:model="automation_mail_id"
        :placeholder="__('Select an automated mail')"
        :options="['' => 'Select an automated mail'] + $campaignOptions"
    />
</div>
