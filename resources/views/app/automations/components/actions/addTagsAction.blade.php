<div>
    <x-mailcoach::text-field
        :label="__('Tags to add')"
        :required="true"
        name="tags"
        wire:model="tags"
    />
</div>
