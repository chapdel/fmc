@props([
    'reverse' => isset($reverse) && $reverse,
    'warning' => isset($warning) && $warning,
    'test' => false,
    'label' => '',
])
<span class="inline-flex {{ $reverse ? 'md:flex-row-reverse' : '' }} gap-2 items-center {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    @if(isset($label))
    <span>
        {{ $label }}
    </span>
    @endisset
    <x-mailcoach::rounded-icon :type="$test ? 'success' : ($warning ? 'warning' : 'error')" :icon="$test ? 'fa-fw fas fa-check' : ($warning ? 'fas fa-exclamation' : 'fas fa-times')"/>
</span>
