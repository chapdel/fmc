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
    'searchable' => true,
])
<div class="mt-12" wire:init="loadRows">
    <div class="table-actions">
        {{ $actions ?? '' }}
        @if ($modelClass)
            @can('create', $modelClass)
                <x-mailcoach::button x-on:click="$store.modals.open('create-{{ $name }}')" :label="__('mailcoach - Create ' . $name)"/>

                <x-mailcoach::modal :title="__('mailcoach - Create ' . $name)" name="create-{{ $name }}">
                    @livewire('mailcoach::create-' . $name)
                </x-mailcoach::modal>
            @endcan
        @endif

        <div class="table-filters">
            @if ($totalRowsCount > 0 && count($filters))
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

            @if($searchable && ($this->isFiltering() || $rows->count()))
                <x-mailcoach::search wire:model="search" :placeholder="__('mailcoach - Search…')"/>
            @endif
        </div>
    </div>

    <div class="w-full text-center mt-6" wire:loading.delay.long>
        <table class="mt-6 table table-fixed">
            <thead>
            <tr>
                @foreach ($columns as $column)
                    <x-mailcoach::th
                        :class="$column['class'] ?? ''"
                        :sort="$this->sort"
                        :property="$column['attribute'] ?? null"
                    >
                        {{ $column['label'] ?? '' }}
                    </x-mailcoach::th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach (range(1, 5) as $i)
                    <tr class="tr-h-double markup-links">
                        @foreach ($columns as $column)
                            @if ($loop->last)
                                <td class="td-action">
                                    <x-mailcoach::dropdown direction="left">
                                        <ul></ul>
                                    </x-mailcoach::dropdown>
                                </td>
                            @else
                                <td>
                                    <div class="animate-pulse w-full h-4 bg-gray-100"></div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div wire:loading.delay.long.remove>
        <table class="mt-6 table table-fixed">
            <thead>
            <tr>
                @foreach ($columns as $column)
                    <x-mailcoach::th
                        :class="$column['class'] ?? ''"
                        :sort="$this->sort"
                        :property="$column['attribute'] ?? null"
                    >
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

    @if ($rows->count())
        <x-mailcoach::table-status
            :name="__('mailcoach - ' . $name)"
            :paginator="$rows"
            :total-count="$totalRowsCount"
            wire:click="clearFilters"
        ></x-mailcoach::table-status>
    @elseif($this->readyToLoad)
        <div class="mt-4">
            @if(isset($empty))
                {{ $empty }}
            @else
                @php($plural = \Illuminate\Support\Str::plural($name))
                @if ($this->search ?? null)
                    <x-mailcoach::help>
                        {{ __("mailcoach - No {$plural} found.") }}
                    </x-mailcoach::help>
                @else
                    <x-mailcoach::help>
                        {!! $emptyText ?? __("mailcoach - No {$plural}.") !!}
                    </x-mailcoach::help>
                @endif
            @endif
        </div>
    @endif
</div>
