<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$row->subscriber_email_list_id, $row->subscriber_id]) }}">
            {{ $row->subscriber_email }}
        </a>
    </td>
    <td class="td-numeric">{{ $row->open_count }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->first_opened_at->toMailcoachFormat() }}</td>
</tr>
