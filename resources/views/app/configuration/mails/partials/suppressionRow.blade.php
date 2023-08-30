<tr>
    <td class="markup-links">
        {{ $row->email }}
    </td>

    <td class="markup-links">
        {{ match($row->stream) {
            'outbound' => 'Transactional',
            'broadcast' => 'Broadcast',
        } }}
    </td>

    <td class="markup-links">
        {{ match($row->reason) {
            'HardBounce' => 'Hard bounce',
            'SpamComplaint' => 'Spam complaint',
            'ManualSuppression' => 'Manual suppression',
        } }}
    </td>

    <td class="td-action text-right">
        <x-mailcoach::confirm-button
            onConfirm="() => $wire.reactivate('{{ $row->stream }}', '{{ $row->email }}')"
            :confirm-text="__mc('Are you sure you want to reactivate this email address?')"
            class="button text-sm py-0 px-3 h-7"
            :disabled="$row->reason === 'SpamComplaint'"
            :title="$row->reason === 'SpamComplaint' ? 'You cannot reactivate spam complaints' : ''"
        >
            Reactivate
        </x-mailcoach::confirm-button>
    </td>
</tr>
