<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="form">
        <x-mailcoach::select-field
            :label="__('Email')"
            name="automation_mail_id"
            wire:model="automation_mail_id"
            :placeholder="__('Select an email')"
            :options="['' => 'Select an email'] + $campaignOptions"
        />
    </x-slot>

    <x-slot name="content">
        @if ($automation_mail_id)
            <div class="tag-neutral">
                {{ optional(\Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::find($automation_mail_id))->name }}
            </div>
        @endif
    </x-slot>
</x-mailcoach::automation-action>
