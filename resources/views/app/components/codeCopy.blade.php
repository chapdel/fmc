@props([
    'code' => '',
    'buttonPosition' => 'bottom',
    'buttonClass' => '',
])
<div {{ $attributes->except('code')->merge(['class' => 'p-2 bg-indigo-50']) }}>
    @if ($buttonPosition === 'top')
        <div x-data class="{{ $buttonClass }}">
            <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">{{ __('mailcoach - Copy') }}</button>
        </div>
    @endif

    <pre class="max-w-full code">{{ $code }}</pre>

    @if ($buttonPosition === 'bottom')
    <div x-data>
        <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">{{ __('mailcoach - Copy') }}</button>
    </div>
    @endif
</div>
