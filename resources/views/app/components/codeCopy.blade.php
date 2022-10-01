@props([
    'code' => '',
    'buttonPosition' => 'bottom',
    'buttonClass' => '',
])
<div {{ $attributes->except('code')->merge(['class' => 'p-2 bg-indigo-50']) }}>
    @if ($buttonPosition === 'top')
        <div x-data class="{{ $buttonClass }} relative z-20">
            <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">{{ __mc('Copy') }}</button>
        </div>
    @endif

    <pre class="max-w-full code overflow-x-auto relative z-10">{{ $code }}</pre>

    @if ($buttonPosition === 'bottom')
    <div x-data>
        <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">{{ __mc('Copy') }}</button>
    </div>
    @endif
</div>
