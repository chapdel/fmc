<fieldset class="form-fieldset">
    @isset($legend)
        <div class="legend">{{ $legend }}</div>
    @endisset
    {{ $slot }}
</fieldset>