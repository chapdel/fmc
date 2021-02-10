<fieldset class="form-fieldset {{ $class ?? '' }}">
    @isset($legend)
        <div class="legend">{{ $legend }}</div>
    @endisset
    {{ $slot }}
</fieldset>