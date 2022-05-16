@php
    $templates = \Spatie\Mailcoach\Domain\Campaign\Models\Template::all()->pluck('name', 'id');
    $templates->prepend('No template', 0)
@endphp

<x-mailcoach::select-field
    class="{{ $attributes->get('class') }}"
    label="Template"
    name="template_id"
    wire:model="templateId"
    :options="$templates"
/>
