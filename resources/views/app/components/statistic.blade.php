<div class="{{ $class ?? '' }} gap-y-1 flex flex-col">
    <div>
        <div class="{{ $numClass ?? 'text-2xl font-semibold' }}">
            {{ $prefix ?? '' }}
            {{ $stat ?? 0 }}
            <span class="font-normal text-gray-400 text-sm">{{ $suffix ?? ''}}</span>
        </div>
        <div class="text-sm flex items-center gap-x-1">
            @if($href ?? null)
                <a class="link-dimmed text-gray-800" href="{{$href}}">{!! $label !!}</a>
            @else
                {!! $label !!}
            @endif
        </div>
    </div>
    @if (! is_null($progress ?? null))
        <div class="progress-bar w-full" @if (!is_null($progressTooltip ?? null)) x-data x-tooltip="'{{ $progressTooltip }}'" @endif>
            <div class="progress-bar-value {{ $progressClass ?? '' }}" style="width: {{ $progress }}%"></div>
        </div>
    @endif
</div>
