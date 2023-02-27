<tr>
    <td>
        {{$row->status_code}}
    </td>
    <td>
        {{$row->event_type}}
    </td>
    <td>
        {{$row->attempt}}
    </td>
    <td>
        {{$row->created_at}}
    </td>
    <td class="markup-links">
        <a href="{{route('webhooks.logs.show', [
            'webhook' => $row->webhookConfiguration,
            'webhookLog' => $row,
        ])}}">
            {{__mc('Details')}}
        </a>
    </td>
</tr>

@php
    ray($row)
@endphp
