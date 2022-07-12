@props([
'rows' => collect(),
'totalRowsCount' => 0,
'name' => '',
'columns' => [],
'filters' => [],
'rowPartial' => null,
'rowData' => [],
'modelClass' => null,
'emptyText' => null,
'noResultsText' => null,
'searchable' => true,
])

@php
    $showFilters = $totalRowsCount > 0 && count($filters);
    $showSearch = $searchable && ($this->isFiltering() || $rows->count())
@endphp
<div wire:init="loadRows" class="card-grid">
    @if (isset($actions) || $modelClass || $showFilters || $showSearch)
    <div class="table-actions">
        {{ $actions ?? '' }}
        @if ($modelClass)
            @can('create', $modelClass)
            <x-mailcoach::button x-on:click="$store.modals.open('create-{{ $name }}')" :label="__('mailcoach - Create ' . $name)" />
            <x-mailcoach::modal :title="__('mailcoach - Create ' . $name)" name="create-{{ $name }}">
                @livewire('mailcoach::create-' . $name)
            </x-mailcoach::modal>
            @endcan
        @endif
        
        <div class="table-filters">
            @if ($showFilters)
            <x-mailcoach::filters>
                @foreach ($filters as $filter)
                @php($attribute = $filter['attribute'])
                <x-mailcoach::filter :current="$this->$attribute" value="{{ $filter['value'] instanceof UnitEnum ? $filter['value']->value : $filter['value'] }}" attribute="{{ $filter['attribute'] }}">
                    {{ $filter['label'] }}
                    @isset($filter['count'])
                    <span class="counter">{{ Illuminate\Support\Str::shortNumber($filter['count']) }}</span>
                    @endisset
                </x-mailcoach::filter>
                @endforeach
            </x-mailcoach::filters>
            @endif

            @if($showSearch)
            <x-mailcoach::search wire:model="search" :placeholder="__('mailcoach - Search…')" />
            @endif
        </div>
    </div>
    @endif

    <div class="card p-0 pb-24 md:pb-0 overflow-x-auto md:overflow-visible">
            <div wire:loading.delay wire:target="loadRows">
                <table class="table table-fixed">
                    <thead>
                        <tr>
                            @foreach ($columns as $column)
                            <x-mailcoach::th :class="$column['class'] ?? ''" :sort="$this->sort" :property="$column['attribute'] ?? null">
                                {{ $column['label'] ?? '' }}
                            </x-mailcoach::th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range(1, 5) as $i)
                        <tr class="markup-links">
                            @foreach ($columns as $column)
                            @if ($loop->last)
                            <td class="td-action"></td>
                            @else
                            <td class="{{ $column['class'] ?? '' }}">
                                <div class="animate-pulse h-4 my-1 bg-gradient-to-r from-indigo-900/5"></div>
                            </td>
                            @endif
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div wire:loading.delay.remove wire:target="loadRows">
                <table class="table table-fixed">
                    <thead>
                        <tr>
                            @foreach ($columns as $column)
                            <x-mailcoach::th :class="$column['class'] ?? ''" :sort="$this->sort" :property="$column['attribute'] ?? null">
                                {{ $column['label'] ?? '' }}
                            </x-mailcoach::th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if ($rows->count())
                        @if($rowPartial)
                        @foreach ($rows as $index => $row)
                        @include($rowPartial, $rowData)
                        @endforeach
                        @endif
                        {{ $tbody ?? '' }}
                        @endif
                    </tbody>
                </table>
            </div>

            @if(!$rows->count() && $this->readyToLoad)
            <div class="p-6 md:px-10">
                @if(isset($empty))
                {{ $empty }}
                @else
                @php($plural = \Illuminate\Support\Str::plural($name))
                @if ($this->search ?? null)
                <x-mailcoach::info>
                    {{ $noResultsText ?? __("mailcoach - No {$plural} found.") }}
                </x-mailcoach::info>
                @else
                <x-mailcoach::info>
                    {!! $emptyText ?? __("mailcoach - No {$plural}.") !!}
                </x-mailcoach::info>
                @endif
                @endif
            </div>
            @endif
        </div>


    @if ($rows->count())
    <x-mailcoach::table-status :name="__('mailcoach - ' . $name)" :paginator="$rows" :total-count="$totalRowsCount" wire:click="clearFilters"></x-mailcoach::table-status>
    @endif

</div>
