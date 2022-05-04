<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button-cancel'])->except(['label']) }}
>
    {{ $label ?? __('mailcoach - Cancel')  }}
</button>
