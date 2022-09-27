<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'button'])->except(['label']) }}
>
    {{ $label ?? __mc('Save')  }}
</button>
