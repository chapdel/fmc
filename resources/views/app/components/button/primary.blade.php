<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'button'])->except(['label']) }}
>
    {{ $label ?? __('mailcoach - Save')  }}
</button>
