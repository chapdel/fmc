<div>
    <x-mailcoach::text-field
        :label="__('Tag')"
        name="tag"
        :value="$automation->trigger->tag ?? null"
        required
    />
</div>
