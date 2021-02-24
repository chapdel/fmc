<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="form">
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
                    <livewire:automation-builder name="yes-actions" :automation="$automation" :actions="$yesActions" :key="\Illuminate\Support\Str::random()" />
                </div>
                <div class="w-full">
                    <h2 class="font-bold mb-2">@lang('Condition fails')</h2>
                    <livewire:automation-builder name="no-actions" :automation="$automation" :actions="$noActions" :key="\Illuminate\Support\Str::random()" />
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
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
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($yesActions as $index => $action)
                                @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                    'index' => $index,
                                    'uuid' => $action['uuid'],
                                    'action' => $action,
                                    'automation' => $automation,
                                    'editable' => false,
                                    'deletable' => false,
                                ], ($action['data'] ?? [])), key(\Illuminate\Support\Str::random()))
                            @endforeach
                        </div>
                    </div>
                </section>
                <section class="px-4 border-l border-gray-300">
                    <h4 class="mb-4 markup-h4"><strong>Condition fails</strong></h4>
                    <div class="grid justify-items-start gap-2">
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($noActions as $index => $action)
                                @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                    'index' => $index,
                                    'uuid' => $action['uuid'],
                                    'action' => $action,
                                    'automation' => $automation,
                                    'editable' => false,
                                    'deletable' => false,
                                ], ($action['data'] ?? [])), key(\Illuminate\Support\Str::random()))
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </x-slot>
</x-mailcoach::automation-action>
