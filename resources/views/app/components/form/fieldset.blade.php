<fieldset class="form-fieldset">
    @isset($legend)
        <legend>{{ $legend }}</legend>
    @endisset
    {{ $slot }}
</fieldset>