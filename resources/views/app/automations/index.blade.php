<x-mailcoach::layout-main :title="__('Automations')">
    <div class="table-actions">
        <x-mailcoach::button data-modal-trigger="create-automation" :label="__('Create automation')"/>

        <x-mailcoach::modal :title="__('Create automation')" name="create-automation" :open="$errors->any()">
            @include('mailcoach::app.automations.partials.create')
        </x-mailcoach::modal>

        @if($automations->count() || $searching)
            <div class="table-filters">
                <x-mailcoach::search :placeholder="__('Filter automations…')"/>
            </div>
        @endif
    </div>

    @if($automations->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                <x-mailcoach::th class="w-4"></x-mailcoach::th>
                <x-mailcoach::th sort-by="name" sort-default>{{ __('Name') }}</x-mailcoach::th>
                <x-mailcoach::th sort-by="-updated_at" class="w-48 th-numeric">{{ __('Last updated') }}</x-mailcoach::th>
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($automations as $automation)
                <tr>
                    <td>
                        <x-mailcoach::form-button :action="route('mailcoach.automations.toggleStatus', $automation)">
                            @if($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::PAUSED)
                                <span title="{{ __('Paused') }}" class="fas fa-pause-circle text-orange-500"></span>
                            @else
                                <span title="{{ __('Sent') }}" class="fas fa-sync fa-spin text-green-500"></span>
                            @endif
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
                                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('Duplicate')" />
                                    </x-mailcoach::form-button>
                                </li>
                                <li>
                                    <x-mailcoach::form-button
                                        :action="route('mailcoach.automations.delete', $automation)"
                                        method="DELETE"
                                        data-confirm="true"
                                        :data-confirm-text="__('Are you sure you want to delete automation :automation?', ['automation' => $automation->name])"
                                    >
                                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('Delete')" :caution="true" />
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
            :name="__('automation|automations')"
            :paginator="$automations"
            :total-count="$totalAutomationsCount"
            :show-all-url="route('mailcoach.automations')"
        />

    @else
        <x-mailcoach::help>
            {{ __('No automations found.') }}
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-main>
