<x-mailcoach::data-table
    name="template"
    :rows="$templates"
    :totalRowsCount="$totalTemplatesCount"
    :columns="[
        ['key' => 'name', 'label' => __('mailcoach - Name')],
        ['key' => 'updated_at', 'label' => __('mailcoach - Last updated'), 'class' => 'w-48 th-numeric'],
    ]"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getTemplateClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-template')" :label="__('mailcoach - Create template')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create template')" name="create-template">
                <livewire:mailcoach::create-template />
            </x-mailcoach::modal>
        @endcan
    @endslot

    @slot('tbody')
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
    @endslot
</x-mailcoach::data-table>
