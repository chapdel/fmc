@props([
    'code' => '',
])
<div {{ $attributes->except('code')->merge(['class' => 'p-2 bg-indigo-50']) }}>
    <pre class="max-w-full code">{{ $code }}</pre>

    <div x-data>
        <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard('{{ $code }}'); $el.innerText = 'Copied!'">{{ __('mailcoach - Copy') }}</button>
    </div>
</div>
