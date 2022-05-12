<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.templates.edit', $row) }}">
            {{ $row->name }}
        </a>
    </td>
    <td class="td-numeric">{{ $row->updated_at->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <button wire:click.prevent="duplicateTemplate({{ $row->id }})">
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                    </button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        onConfirm="() => $wire.deleteTemplate({{ $row->id }})"
                        :confirm-text="__('mailcoach - Are you sure you want to delete template :template?', ['template' => $row->name])"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true" />
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
