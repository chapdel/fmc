<div>
    <div class="table-actions">
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getTemplateClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-template')" :label="__('mailcoach - Create template')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create template')" name="create-template" :open="$errors->any()">
                <livewire:mailcoach::create-template />
            </x-mailcoach::modal>
        @endcan

        @if($templates->count() || $this->search)
            <div class="table-filters">
                <x-mailcoach::search wire:model="filter.search" :placeholder="__('mailcoach - Filter templates…')"/>
            </div>
        @endif
    </div>

    @if($templates->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                <x-mailcoach::th :sort="$sort" property="name">
                    {{ __('mailcoach - Name') }}
                </x-mailcoach::th>
                <x-mailcoach::th class="w-48 th-numeric" :sort="$sort" property="updated_at">
                    {{ __('mailcoach - Last updated') }}
                </x-mailcoach::th>
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($templates as $template)
                <tr>
                    <td class="markup-links">
                        <a class="break-words" href="{{ route('mailcoach.templates.edit', $template) }}">
                            {{ $template->name }}
                        </a>
                    </td>
                    <td class="td-numeric">{{ $template->updated_at->toMailcoachFormat() }}</td>
                    <td class="td-action">
                        <x-mailcoach::dropdown direction="left">
                            <ul>
                                <li>
                                    <x-mailcoach::form-button
                                        :action="route('mailcoach.templates.duplicate', $template)"
                                    >
                                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                                    </x-mailcoach::form-button>
                                </li>
                                <li>
                                    <x-mailcoach::confirm-button
                                        onConfirm="() => $wire.deleteTemplate({{ $template->id }})"
                                        :confirm-text="__('mailcoach - Are you sure you want to delete template :template?', ['template' => $template->name])"
                                    >
                                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true" />
                                    </x-mailcoach::confirm-button>
                                </li>
                            </ul>
                        </x-mailcoach::dropdown>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <x-mailcoach::table-status
            :name="__('mailcoach - template|templates')"
            :paginator="$templates"
            :total-count="$totalTemplatesCount"
            wire:click="clearFilters"
        ></x-mailcoach::table-status>
    @else
        <x-mailcoach::help>
            @if ($this->search)
                {{ __('mailcoach - No templates found.') }}
            @else
                {{ __('mailcoach - DRY? No templates here.') }}
            @endif
        </x-mailcoach::help>
    @endif
</div>
