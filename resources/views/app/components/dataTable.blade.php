@props([
    'rows' => collect(),
    'totalRowsCount' => 0,
    'name' => '',
    'columns' => [],
    'filters' => [],
    'rowPartial' => null,
])
<div>
    <div class="table-actions">
        {{ $actions }}

        <div class="table-filters">
            @if (count($filters))
                <x-mailcoach::filters>
                    @foreach ($filters as $filter)
                        <x-mailcoach::filter :filter="$this->filter" value="{{ $filter['value'] }}" attribute="{{ $filter['attribute'] }}">
                            {{ $filter['label'] }}
                            @isset($filter['count'])
                                <span class="counter">{{ Illuminate\Support\Str::shortNumber($filter['count']) }}</span>
                            @endisset
                        </x-mailcoach::filter>
                    @endforeach
                </x-mailcoach::filters>
            @endif

            @if(($this->filter['search'] ?? null) || $rows->count())
                <x-mailcoach::search wire:model="filter.search" :placeholder="__('mailcoach - Search…')"/>
            @endif
        </div>
    </div>

    @if($rows->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                @foreach ($columns as $column)
                    <x-mailcoach::th
                        :class="$column['class'] ?? ''"
                        :sort="$this->sort"
                        :property="$column['key'] ?? null"
                    >
                        {{ $column['label'] ?? '' }}
                    </x-mailcoach::th>
                @endforeach
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
                @if($rowPartial)
                    @foreach ($rows as $row)
                        @include($rowPartial)
                    @endforeach
                @endif
                {{ $tbody ?? '' }}
            </tbody>
        </table>

        <x-mailcoach::table-status
            :name="__('mailcoach - ' . $name)"
            :paginator="$rows"
            :total-count="$totalRowsCount"
            wire:click="clearFilters"
        ></x-mailcoach::table-status>
    @else
        @if(isset($empty))
        @else
            <x-mailcoach::help>
                {{ __("mailcoach - No {$name} found.") }}
            </x-mailcoach::help>
        @endif
    @endif
</div>
