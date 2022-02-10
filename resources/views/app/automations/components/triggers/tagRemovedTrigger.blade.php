<div>
    <x-mailcoach::text-field
        :label="__('mailcoach - Tag')"
        name="tag"
        :value="$automation->getTrigger()->tag ?? null"
        required
    />
</div>
