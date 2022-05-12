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
<div wire:init="loadRows">
    @if (isset($actions) || ($totalRowsCount > 0 && count($filters)) || ($searchable  && (($this->filter['search'] ?? null) || ($this->filter['status'] ?? null) || $rows->count())))
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
                            <x-mailcoach::filter :filter="$this->filter" value="{{ $filter['value'] }}" attribute="{{ $filter['attribute'] }}">
                                {{ $filter['label'] }}
                                @isset($filter['count'])
                                    <span class="counter">{{ Illuminate\Support\Str::shortNumber($filter['count']) }}</span>
                                @endisset
                            </x-mailcoach::filter>
                        @endforeach
                    </x-mailcoach::filters>
                @endif

                @if($searchable && (($this->filter['search'] ?? null) || ($this->filter['status'] ?? null) || $rows->count()))
                    <x-mailcoach::search wire:model="filter.search" :placeholder="__('mailcoach - Search…')"/>
                @endif
            </div>
        </div>
    @endif

    <div class="w-full text-center" wire:loading.delay.long>
        <style>
            @keyframes loadingpulse {
                0%   {transform: scale(.8); opacity: .75}
                100% {transform: scale(1.5); opacity: .9}
            }
        </style>
        <span
            style="animation: loadingpulse 0.5s alternate infinite ease-in-out;"
            class="group w-10 h-10 inline-flex my-16 items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
            <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                @include('mailcoach::app.layouts.partials.logoSvg')
            </span>
        </span>
    </div>

    <div wire:loading.delay.long.remove>

        @if($rows->count())
            <table class="table table-fixed">
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
                    @if($rowPartial)
                        @foreach ($rows as $index => $row)
                            @include($rowPartial, $rowData)
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
        @elseif($this->readyToLoad)
            <div wire:loading.remove>
                @if(isset($empty))
                    {{ $empty }}
                @else
                    @php($plural = \Illuminate\Support\Str::plural($name))
                    @if ($this->filter['search'] ?? null)
                        <x-mailcoach::help>
                            {{ __("mailcoach - No {$plural} found.") }}
                        </x-mailcoach::help>
                    @else
                        <x-mailcoach::help>
                            {{ $emptyText ?? __("mailcoach - No {$plural}.") }}
                        </x-mailcoach::help>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>
