<tr class="markup-links">
    <td>
        <a class="break-words" href="{{ route('mailcoach.emailLists.segment.edit', [$row->emailList, $row]) }}">
            {{ $row->name }}
        </a>
    </td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->created_at->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <x-mailcoach::form-button
                        :action="route('mailcoach.emailLists.segment.duplicate', [$row->emailList, $row])"
                    >
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                    </x-mailcoach::form-button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__('mailcoach - Are you sure you want to delete segment :segmentName?', ['segmentName' => $row->name])"
                        onConfirm="() => $wire.deleteSegment({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
