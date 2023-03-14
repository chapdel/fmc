<tr>
    <td class="markup-links">
        <a href="{{route('webhooks.logs.show', [
            'webhook' => $row->webhookConfiguration,
            'webhookLog' => $row,
        ])}}">
            {{  $row->created_at }}
        </a>
    </td>
    <td>
        <span class="inline-flex items-center">
            <x-mailcoach::rounded-icon
                :type="$row->wasSuccessful() ? 'success' : 'error'"
                :icon="$row->wasSuccessful() ? 'fa-fw fas fa-check' : 'fas fa-times'"
            />
            <span class="pl-2">{{ $row->status_code }}</span>
        </span>
    </td>
    <td>
        {{ Str::remove('Event', $row->event_type) }}
    </td>
    <td>
        {{  $row->attempt ?? __mc('Manual') }}
    </td>
    <td class="markup-links">
        <a href="#" wire:click.prevent="resend('{{  $row->uuid }}')">
            {{  __mc('Resend') }}
        </a>
    </td>
</tr>
