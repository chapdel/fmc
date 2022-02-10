<div>
    <x-mailcoach::date-time-field
        :label="__('mailcoach - Date')"
        name="date"
        :value="$automation->getTrigger()->date ?? null"
        required
    />
</div>
