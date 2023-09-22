@component('mailcoach::mails.layout.message')
{{ __mc('Hi') }},

{{ __mc('Campaign **:campaignName** was sent to **:numberOfSubscribers** subscribers (list :emailListName) on :sentAt', [
    'campaignName' => $campaign->name,
    'numberOfSubscribers' => ($campaign->contentItem->sent_to_number_of_subscribers ?? 0 ),
    'emailListName' => $campaign->emailList->name,
    'sentAt' => $campaign->sent_at->toMailcoachFormat()
]) }}.

<table class="stats">
<tr>
@if ($campaign->contentItem->open_count)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.opens', $campaign),
'stat' => $campaign->contentItem->open_count,
'label' => __mc('Opens'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->contentItem->unique_open_count,
'label' => __mc('Unique Opens'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->contentItem->open_rate / 100), 2),
'suffix' => '%',
'label' => __mc('Open Rate'),
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">{{ __mc('Opens not tracked') }}</div>
</td>
@endif
</tr>

<tr>
@if($campaign->contentItem->click_count)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.clicks', $campaign),
'stat' => $campaign->contentItem->click_count,
'label' => __mc('Clicks'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->contentItem->unique_click_count,
'label' => __mc('Unique Clicks'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->contentItem->click_rate / 100), 2),
'suffix' => '%',
'label' => __mc('Clicks Rate'),
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">{{ __mc('Clicks not tracked') }}</div>
</td>
@endif
</tr>

<tr>
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.unsubscribes', $campaign),
'stat' => $campaign->contentItem->unsubscribe_count,
'label' => __mc('Unsubscribes'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->contentItem->unsubscribe_rate / 100), 2),
'label' => __mc('Unsubscribe Rate'),
'suffix' => '%'
])
</td>
<td></td>
</tr>

<tr>
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.outbox', $campaign),
'stat' => $campaign->contentItem->bounce_count,
'label' => 'Bounces',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->contentItem->bounce_rate / 100), 2),
'label' => 'Bounce Rate',
'suffix' => '%'
])
</td>
<td></td>
</tr>
</table>

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
{{ __mc('View summary') }}
@endcomponent

@component('mailcoach::mails.layout.subcopy')
[{{ __mc('Edit notification settings') }}]({{ $settingsUrl }})
@endcomponent
@endcomponent
