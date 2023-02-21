<tr>
    <td class="markup-links">
        <a href="{{ route('webhooks.edit', $row) }}">
            {{ $row->name }}
        </a>
    </td>
    <td>
        {{ $row->url }}
    </td>
    <td>
        @if ($row->use_for_all_lists)
            <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
        @else
            <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
        @endif
    </td>
    @if(config('mailcoach.webhooks.selectable_event_types_enabled', false))
    <td>
       @if ($row->use_for_all_events || $row->events->count() === 6)
            All
       @else
            {{ $row->events->count() }} / 6
       @endif
    </td>
    @endif
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete webhook: :webhook?', ['webhook' => $row->name])"
                        onConfirm="() => $wire.deleteWebhook({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :caution="true" :text="__mc('Delete')" />
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
