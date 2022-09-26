@pushonce('endHead')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
    <script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
@endpushonce

<div class="form-field" wire:ignore>
    @if($label ?? null)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="color-{{ $name }}">
            {{ $label }}
        </label>
    @endif

    <!-- search is a prefix here because otherwise 1Password shows its widget -->
    <input
        autocomplete="off"
        @if (! $attributes->has('x-bind:type'))
            type="{{ $type ?? 'text' }}"
        @endif
        name="search-{{ $name }}"
        id="search-{{ $name }}"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ old($name, $value ?? '') }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled ?? false) disabled @endif
        data-coloris
    >
    @error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
