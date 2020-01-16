<form method="POST" action="{{ $action }}" @isset($dataConfirm) data-confirm="true" @endisset>
    @csrf
    @method($method ?? 'POST')
        <button
            type="submit"
            class="{{ $class ?? '' }}"
        >
            {{ $slot }}
        </button>
</form>
