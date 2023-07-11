@props([
    'placeholder' => '',
    'value' => '',
])
<div class="search {{ $class ?? '' }}">
    <input
        {{ $attributes->except('class') }}
        type="search"
        required
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
    >
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
</div>
