<x-mailcoach::fieldset>
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold bg-yellow-200 text-yellow-600 shadow">
                {{ $index + 1 }}
            </span>
            <span>
                {{ $action['class']::getName() }}
            </span>
        </header>
    </x-slot>

    <div class="relative">
        <div class="flex items-center absolute top-4 right-4 space-x-3 z-20">
            @if ($editing && count($editingActions) === 0)
                <button type="button" wire:click="save">
                    <i class="icon-button hover:text-green-500 fas fa-check"></i>
                </button>
            @elseif ($editable && !$editing)
                <button type="button" wire:click="edit">
                    <i class="icon-button far fa-edit"></i>
                </button>
            @endif
            @if ($deletable)
                <button type="button" wire:click="delete">
                    <i class="icon-button hover:text-red-500 far fa-trash-alt"></i>
                </button>
            @endif
        </div>

        <div class="relative z-10">
            @if ($editing)
                <div class="mb-4">
                    <div>
                        <div class="grid gap-4">
                            <label>
                                {{ __('Duration to check before failing the condition.') }}
                            </label>
                            <div class="flex gap-2">
                                <x-mailcoach::text-field
                                    :label="__('Length')"
                                    :required="true"
                                    name="length"
                                    wire:model="length"
                                    type="number"
                                />
                                <x-mailcoach::select-field
                                    :label="__('Unit')"
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

                            <x-mailcoach::select-field
                                :label="__('Condition')"
                                name="condition"
                                wire:model="condition"
                                :placeholder="__('Select a condition')"
                                :options="$conditionOptions"
                            />

                            @switch ($condition)
                                @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition::class)
                                <x-mailcoach::text-field
                                    :label="__('Tag')"
                                    name="conditionData.tag"
                                    wire:model="conditionData.tag"
                                />
                                @break
                                @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail::class)
                                <x-mailcoach::select-field
                                    :label="__('Automation mail')"
                                    name="conditionData.automation_mail_id"
                                    wire:model="conditionData.automation_mail_id"
                                    :placeholder="__('Select a mail')"
                                    :options="\Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::pluck('name', 'id')"
                                />
                                @break
                                @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail::class)
                                <x-mailcoach::select-field
                                    :label="__('Automation mail')"
                                    name="conditionData.automation_mail_id"
                                    wire:model="conditionData.automation_mail_id"
                                    :placeholder="__('Select a mail')"
                                    :options="\Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::pluck('name', 'id')"
                                />

                                @if ($conditionData['automation_mail_id'])
                                    <x-mailcoach::select-field
                                        :label="__('Link')"
                                        name="conditionData.automation_mail_link_url"
                                        wire:model="conditionData.automation_mail_link_url"
                                        :placeholder="__('Select a link')"
                                        :options="
                                    \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::find($conditionData['automation_mail_id'])
                                        ->htmlLinks()
                                        ->mapWithKeys(fn ($url) => [$url => $url])
                                        ->toArray()
                                "
                                    />
                                @endif
                                @break
                            @endswitch
                        </div>

                        <div class="grid grid-cols-2 mt-4 gap-6 w-full">
                            <div class="w-full">
                                <h2 class="font-bold mb-2">@lang('Condition passes')</h2>
                                <livewire:automation-builder name="{{ $uuid }}-yes-actions" :automation="$automation" :actions="$yesActions" key="{{ $uuid }}-yes-actions" />
                            </div>
                            <div class="w-full">
                                <h2 class="font-bold mb-2">@lang('Condition fails')</h2>
                                <livewire:automation-builder name="{{ $uuid }}-no-actions" :automation="$automation" :actions="$noActions" key="{{ $uuid}}-no-actions" />
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="">
                    <h4 class="mb-4 markup-h4">
                        Checking for {{ \Carbon\CarbonInterval::createFromDateString("{$length} {$unit}") }}<br>
                        @if ($condition)
                            {{ $condition::getName() }}: <span class="tag-neutral">{{ $condition::getDescription($conditionData) }}</span>
                        @endif
                    </h4>
                    <div class="grid grid-cols-2 gap-6">
                        <section class="px-4 border-l border-gray-300">
                            <h4 class="mb-4 markup-h4"><strong>Condition passes</strong></h4>
                            <div class="grid justify-items-start gap-2">
                                <div class="grid grid-cols-1 gap-4 w-full">
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
                        <section class="px-4 border-l border-gray-300">
                            <h4 class="mb-4 markup-h4"><strong>Condition fails</strong></h4>
                            <div class="grid justify-items-start gap-2">
                                <div class="grid grid-cols-1 gap-4 w-full">
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

                <dl class="mt-4 dl text-xs">
                    <dt>Active</dt>
                    <dd>{{ $action['active'] ?? 0 }}</dd>
                    <dt>Completed</dt>
                    <dd>{{ $action['completed'] ?? 0 }}</dd>
                </dl>
            @endif
        </div>
    </div>
</x-mailcoach::fieldset>

