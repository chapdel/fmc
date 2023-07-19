<tr class="markup-links">
    @if ($selectable)
        <td class="text-xs !pt-4">
            <x-mailcoach::checkbox-field name="select-{{ $row->id }}" label="" :checked="in_array($row->id, $this->selectedRows)" wire:change="select('{{ $row->id }}')" />
        </td>
    @endif
    <td>
        @if ($row->subscriber)
            <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$row->subscriber->emailList, $row->subscriber]) }}">{{ $row->subscriber->email }}</a>
        @else
            &lt;{{ __mc('deleted subscriber') }}&gt;
        @endif
    </td>
    <td>{{ $row->failure_reason }}{{optional($row->latestFeedback())->formatted_type }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ optional($row->sent_at)->toMailcoachFormat() ?? '-' }}</td>
</tr>
