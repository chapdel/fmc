<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.summary', $row) }}">
            {{ $row->name }}
        </a>
    </td>
    <td class="td-numeric">{{ number_format($row->active_subscribers_count) }}</td>
    <td class="td-numeric hidden | xl:table-cell">
        {{ $row->created_at->toMailcoachFormat() }}
    </td>
    <td class="td-action">
        <x-mailcoach::confirm-button
            :confirm-text="__('mailcoach - Are you sure you want to delete list :emailListName?', ['emailListName' => $row->name])"
            onConfirm="() => $wire.deleteList({{ $row->id }})"
            class="icon-button hover:text-red-500"
        >
            <i class="far fa-trash-alt"></i>
        </x-mailcoach::confirm-button>
    </td>
</tr>
