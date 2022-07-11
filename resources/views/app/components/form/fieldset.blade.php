@props([
    'card' => false,
    'focus' => false,
    'class' => '',
    'legend' => null
])

<fieldset {{ $attributes->except('class') }} class="{{ $card? 'card form-grid' : 'form-fieldset' }} {{ $class }} {{ $focus ? 'form-fieldset-focus' : '' }}">
    @isset($legend)
        <div class="form-legend">
            {{ $legend }}
        </div>
    @endisset
    {{ $slot }}
</fieldset>
