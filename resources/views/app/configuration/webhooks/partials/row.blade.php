<tr>
    <td class="markup-links">
        @if ($row->enabled)
            <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
        @else
            <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
        @endif

        <a href="{{ route('webhooks.edit', $row) }}" class="ml-2">
            {{ $row->name }}
        </a>

        <div class="text-gray-400 mt-2 text-sm">
            {{ $row->url }}
        </div>
    </td>
    <td>
        @if ($row->use_for_all_lists)
            <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
        @else
            <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
        @endif
    </td>
    @if($row->selectableEventsEnabled())
    <td>
       @if ($row->useForAllEvents() || $row->events->count() === $row->countSelectableEventTypes())
            <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
       @else
            <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
       @endif
    </td>
    @endif
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    @if (config('mailcoach.webhooks.logs', false))
                        <a href="{{ route('webhooks.logs.index', $row) }}">
                            <x-mailcoach::icon-label icon="far fa-scroll" :text="__mc('View logs')" />
                        </a>
                    @endif

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
