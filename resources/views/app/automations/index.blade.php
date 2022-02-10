<x-mailcoach::layout-main :title="__('mailcoach - Automations')">
    <div class="table-actions">
        <x-mailcoach::button data-modal-trigger="create-automation" :label="__('mailcoach - Create automation')"/>

        <x-mailcoach::modal :title="__('mailcoach - Create automation')" name="create-automation" :open="$errors->any()">
            @include('mailcoach::app.automations.partials.create')
        </x-mailcoach::modal>

        @if($automations->count() || $searching)
            <div class="table-filters">
                <x-mailcoach::search :placeholder="__('mailcoach - Filter automations…')"/>
            </div>
        @endif
    </div>

    @if($automations->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                <x-mailcoach::th class="w-4"></x-mailcoach::th>
                <x-mailcoach::th sort-by="name" sort-default>{{ __('mailcoach - Name') }}</x-mailcoach::th>
                <x-mailcoach::th sort-by="-updated_at" class="w-48 th-numeric">{{ __('mailcoach - Last updated') }}</x-mailcoach::th>
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($automations as $automation)
                <tr>
                    <td class="group align-center">
                        <div class="w-5 h-5">
                        <x-mailcoach::form-button :action="route('mailcoach.automations.toggleStatus', $automation)">
                            @if($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::PAUSED)
                                <span class="group-hover:opacity-0 fas fa-magic text-gray-400"></span>
                                <span title="{{ __('mailcoach - Start Automation') }}" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100" >
                                    <x-mailcoach::rounded-icon class="w-5 h-5" type="success" icon="fas fa-play"/>
                                </span>
                            @else
                                <span class="group-hover:opacity-0 fas fa-sync fa-spin text-green-500"></span>
                                <span title="{{ __('mailcoach - Pause Automation') }}" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100" >
                                    <x-mailcoach::rounded-icon class="w-5 h-5" type="warning" icon="fas fa-pause"/>
                                </span>
                            @endif
                            </div>
                        </x-mailcoach::form-button>
                    </td>
                    <td class="markup-links">
                        <a class="break-words" href="{{ route('mailcoach.automations.settings', $automation) }}">
                            {{ $automation->name }}
                        </a>
                    </td>
                    <td class="td-numeric">{{ $automation->updated_at->toMailcoachFormat() }}</td>
                    <td class="td-action">
                        <x-mailcoach::dropdown direction="left">
                            <ul>
                                <li>
                                    <x-mailcoach::form-button
                                        :action="route('mailcoach.automations.duplicate', $automation)"
                                    >
                                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                                    </x-mailcoach::form-button>
                                </li>
                                <li>
                                    <x-mailcoach::form-button
                                        :action="route('mailcoach.automations.delete', $automation)"
                                        method="DELETE"
                                        data-confirm="true"
                                        :data-confirm-text="__('mailcoach - Are you sure you want to delete automation :automation?', ['automation' => $automation->name])"
                                    >
                                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true" />
                                    </x-mailcoach::form-button>
                                </li>
                            </ul>
                        </x-mailcoach::dropdown>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <x-mailcoach::table-status
            :name="__('mailcoach - automation|automations')"
            :paginator="$automations"
            :total-count="$totalAutomationsCount"
            :show-all-url="route('mailcoach.automations')"
        />

    @else
        <x-mailcoach::help>
            {{ __('mailcoach - No automations found.') }}
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-main>
