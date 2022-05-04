<tr class="markup-links">
    <td><a href="{{ route('mailcoach.transactionalMails.templates.edit', $row) }}">{{ $row->name }}</a></td>

    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <x-mailcoach::form-button
                        :action="route('mailcoach.transactionalMails.templates.duplicate', $row)"
                    >
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                    </x-mailcoach::form-button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__('mailcoach - Are you sure you want to delete template :template?', ['template' => $row->name])"
                        onConfirm="() => $wire.deleteTemplate({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true" />
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
