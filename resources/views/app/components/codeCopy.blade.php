@props([
    'code' => '',
])
<div {{ $attributes->except('code')->merge(['class' => 'p-2 bg-indigo-50']) }}>
    <pre class="max-w-full code">{{ $code }}</pre>

    <div x-data>
        <button class="text-sm link-dimmed" @click="$clipboard('{{ $code }}'); $el.innerText = 'Copied!'">Copy to clipboard</button>
    </div>
</div>
