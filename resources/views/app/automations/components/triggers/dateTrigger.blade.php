<div>
    <x-mailcoach::date-time-field
        :label="__('Date')"
        name="date"
        :value="$automation->trigger->date ?? null"
        required
    />
</div>
