<button 
    type="{{ $type ?? 'submit'}}" 
    class="{{ (isset($secondary) && $secondary) ? 'button-secondary' : 'button'  }}" 
    data-modal-trigger="{{ $dataModalTrigger ?? '' }}"
    {{ (isset($disabled) && $disabled) ? 'disabled' : '' }}
>
    {{ $label ?? __('Save')  }}
</button>