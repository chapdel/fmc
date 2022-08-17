<x-mailcoach::fieldset card :focus="$editing">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                {{ $index + 1 }}
            </span>
            <span class="font-normal whitespace-nowrap">
                Check for
                <span class="form-legend-accent">
                    {{ \Carbon\CarbonInterval::createFromDateString("{$length} {$unit}") }}
                </span>
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 gap-4 z-10">
        @if ($editing && count($editingActions) === 0)
            <button type="button" wire:click="save" class="hover:text-green-500">
                <i class="icon-button fas fa-check"></i>
                Save
            </button>
        @elseif ($editable && !$editing)
            <button type="button" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable && count($editingActions) === 0)
            <button type="button" onclick="confirm('{{ __('mailcoach - Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete">
                <i class="icon-button link-danger far fa-trash-alt"></i>
            </button>
        @endif
    </div>

        <div class="grid gap-6">
            @if ($editing)
                <div class="form-grid">
                    <div class="form-actions">
                        <div class="col-span-8 sm:col-span-4">
                            <x-mailcoach::text-field
                                :label="__('mailcoach - Duration')"
                                :required="true"
                                name="length"
                                wire:model="length"
                                type="number"
                            />
                        </div>
                        <div class="col-span-4 sm:col-span-4">
                            <x-mailcoach::select-field
                                :label="__('mailcoach - Unit')"
                                :required="true"
                                name="unit"
                                wire:model="unit"
                                :options="
                                    collect($units)
                                        ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                                        ->toArray()
                                "
                            />
                        </div>

                        <div class="col-span-12 sm:col-span-4 sm:col-start-1">
                            <x-mailcoach::select-field
                                :label="__('mailcoach - Condition')"
                                :required="true"
                                name="condition"
                                wire:model="condition"
                                :placeholder="__('mailcoach - Select a condition')"
                                :options="$conditionOptions"
                            />
                        </div>

                        @switch ($condition)
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::tags-field
                                        :label="__('mailcoach - Tag')"
                                        name="conditionData.tag"
                                        wire:model="conditionData.tag"
                                        :allow-create="true"
                                        :tags="$automation->emailList?->tags()->pluck('name')->toArray() ?? []"
                                    />
                                </div>
                            @break
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::select-field
                                        :label="__('mailcoach - Automation mail')"
                                        name="conditionData.automation_mail_id"
                                        wire:model="conditionData.automation_mail_id"
                                        :placeholder="__('mailcoach - Select a mail')"
                                        :options="
                                            \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()->orderBy('name')->pluck('name', 'id')
                                        "
                                    />
                                </div>
                            @break
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::select-field
                                        :label="__('mailcoach - Automation mail')"
                                        name="conditionData.automation_mail_id"
                                        wire:model="conditionData.automation_mail_id"
                                        :placeholder="__('mailcoach - Select a mail')"
                                        :required="true"
                                        :options="
                                            \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        "
                                    />
                                </div>

                                @if ($conditionData['automation_mail_id'])
                                    <div class="col-span-12 sm:col-span-4">
                                        <x-mailcoach::select-field
                                            :label="__('mailcoach - Link')"
                                            name="conditionData.automation_mail_link_url"
                                            wire:model="conditionData.automation_mail_link_url"
                                            :placeholder="__('mailcoach - Select a link')"
                                            :required="false"
                                            :options="
                                                ['' => __('mailcoach - Any link')] +
                                                \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::find($conditionData['automation_mail_id'])
                                                    ->htmlLinks()
                                                    ->mapWithKeys(fn ($url) => [$url => $url])
                                                    ->toArray()
                                            "
                                        />
                                    </div>
                                @endif
                            @break
                        @endswitch
                    </div>
                </div>

                <div class="grid gap-6 w-full">
                    <section class="bg-indigo-900/5 before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-green-500 before:to-green-500/70 before:rounded-l-md">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-indigo-700/20 border-r border-t border-b rounded">
                            <div class="flex items-center">
                                <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-green-500/10 text-white rounded-br space-x-2">
                                    <i class="far fa-thumbs-up"></i>
                                    <span class="markup-h4">@lang('If')</span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($yesActions) }} {{ trans_choice('mailcoach - action|actions', count($yesActions)) }}</span>
                                <button class="ml-auto -mr-8 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-yes-actions" :automation="$automation" :actions="$yesActions" key="{{ $uuid }}-yes-actions" />
                            </div>
                        </div>
                    </section>
                    <section class="bg-indigo-900/5 before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-red-500 before:to-red-500/70 before:rounded-l-md">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-indigo-700/20 border-r border-t border-b rounded">
                            <div class="flex items-center">
                                <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-red-500/10 text-white rounded-br space-x-2">
                                    <i class="far fa-thumbs-down"></i>
                                    <span class="markup-h4">@lang('Else')</span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($noActions) }} {{ trans_choice('mailcoach - action|actions', count($noActions)) }}</span>
                                <button class="ml-auto -mr-8 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-no-actions" :automation="$automation" :actions="$noActions" key="{{ $uuid}}-no-actions" />
                            </div>
                        </div>
                    </section>
                </div>
            @else
                <div class="grid gap-6 flex-grow">
                    <div class="grid gap-6 w-full">
                        <section class="bg-indigo-900/5 before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-green-500 before:to-green-500/70 before:rounded-l-md">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-indigo-700/20 border-r border-t border-b rounded-r">
                                <div class="flex items-center">
                                    <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-green-500/10 text-white rounded-br space-x-2">
                                        <i class="far fa-thumbs-up"></i>
                                         @if ($condition)
                                            <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                                <span class="font-normal">@lang('If') {{ $condition::getName() }}</span>
                                                <span class="font-semibold tracking-normal normal-case">{{ $condition::getDescription($conditionData) }}</span>?
                                            </span>
                                        @endif
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($yesActions) }} {{ trans_choice('mailcoach - action|actions', count($yesActions)) }}</span>
                                    <button class="ml-auto -mr-8 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="!collapsed">
                                    @foreach ($yesActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('yes' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        <section class="bg-indigo-900/5 before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-red-500 before:to-red-500/70 before:rounded-l-md">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 pb-8 border-indigo-700/20 border-r border-t border-b rounded-r">
                                <div class="flex items-center">
                                    <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-red-500/10 text-white rounded-br space-x-2">
                                        <i class="far fa-thumbs-down"></i>
                                        <span class="markup-h4">
                                            <span class="font-normal">@lang('Else')</span>
                                        </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($noActions) }} {{ trans_choice('mailcoach - action|actions', count($noActions)) }}</span>
                                    <button class="ml-auto -mr-8 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="!collapsed">
                                    @foreach ($noActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('no' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
</x-mailcoach::fieldset>

