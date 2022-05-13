<tr>
    <td class="markup-links">
        @if ($row->concernsCampaign())
            <a class="break-words" href="{{ route('mailcoach.campaigns.summary', $row->campaign) }}">
                {{ $row->campaign->name }}
            </a>
        @elseif ($row->concernsAutomationMail())
            <a class="break-words" href="{{ route('mailcoach.automations.mails.summary', $row->automationMail) }}">
                {{ $row->automationMail->name }}
            </a>
        @elseif ($row->concernsTransactionalMail())
            <a class="break-words" href="{{ route('mailcoach.transactionalMail.show', $row->transactionalMail) }}">
                {{ $row->transactionalMail->name }}
            </a>
        @endif
    </td>
    <td class="td-numeric">{{ $row->opens_count }}</td>
    <td class="td-numeric">{{ $row->clicks_count }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->sent_at?->toMailcoachFormat() }}</td>
</tr>
