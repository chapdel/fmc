@php
    /** @var \Illuminate\Support\Collection $templates */
    $templates = \Spatie\Mailcoach\Mailcoach::getTemplateClass()::all()->pluck('name', 'id');
@endphp

<div>
    @if(count($templates))
        <x-mailcoach::combo-box-field
            class="{{ $attributes->get('class') }}"
            label="Template"
            name="template_id"
            wire:model.live="templateId"
            :clearable="$attributes->get('clearable', true )"
            :placeholder="__mc('No template')"
            :options="$templates"
        />
        <x-mailcoach::select-field
            class="{{ $attributes->get('class') }}"
            label="Template"
            name="template_id"
            wire:model.live="templateId"
            :clearable="$attributes->get('clearable', true )"
            :placeholder="__mc('No template')"
            :options="$templates"
        />
    @else
        <div class="form-field">
            <label class="label">Template</label>
            <div>
                No templates yet, go <a class="link-dimmed" href="{{ route('mailcoach.templates') }}">create one</a>.
            </div>
        </div>
    @endif
</div>
