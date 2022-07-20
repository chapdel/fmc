@php
    /** @var \Illuminate\Support\Collection $templates */
    $templates = \Spatie\Mailcoach\Mailcoach::getTemplateClass()::all()->pluck('name', 'id');
@endphp

<x-mailcoach::select-field
    class="{{ $attributes->get('class') }}"
    label="Template"
    name="template_id"
    wire:model="templateId"
    :clearable="true"
    :placeholder="__('mailcoach - No template')"
    :options="$templates"
/>
