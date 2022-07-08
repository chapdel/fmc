@component('mailcoach::mails.layout.message')
{{ __('mailcoach - Hi') }},

{{ __('mailcoach - Campaign **:campaignName** was sent to **:numberOfSubscribers** subscribers (list :emailListName) on :sentAt', ['campaignName'=>$campaign->name,'numberOfSubscribers'=>($campaign->sent_to_number_of_subscribers ?? 0 ),'emailListName'=>$campaign->emailList->name,'sentAt'=>$campaign->sent_at->toMailcoachFormat()]) }}.

<table class="stats">
<tr>
@if ($campaign->open_count)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.opens', $campaign),
'stat' => $campaign->open_count,
'label' => __('mailcoach - Opens'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->unique_open_count,
'label' => __('mailcoach - Unique Opens'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->open_rate / 100), 2),
'suffix' => '%',
'label' => __('mailcoach - Open Rate'),
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">{{ __('mailcoach - Opens not tracked') }}</div>
</td>
@endif
</tr>

<tr>
@if($campaign->click_count)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.clicks', $campaign),
'stat' => $campaign->click_count,
'label' => __('mailcoach - Clicks'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->unique_click_count,
'label' => __('mailcoach - Unique Clicks'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->click_rate / 100), 2),
'suffix' => '%',
'label' => __('mailcoach - Clicks Rate'),
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">{{ __('mailcoach - Clicks not tracked') }}</div>
</td>
@endif
</tr>

<tr>
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.unsubscribes', $campaign),
'stat' => $campaign->unsubscribe_count,
'label' => __('mailcoach - Unsubscribes'),
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->unsubscribe_rate / 100), 2),
'label' => __('mailcoach - Unsubscribe Rate'),
'suffix' => '%'
])
</td>
<td></td>
</tr>

<tr>
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.outbox', $campaign),
'stat' => $campaign->bounce_count,
'label' => 'Bounces',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => number_format(($campaign->bounce_rate / 100), 2),
'label' => 'Bounce Rate',
'suffix' => '%'
])
</td>
<td></td>
</tr>
</table>

@component('mailcoach::mails.layout.button', ['url' => $summaryUrl])
{{ __('mailcoach - View summary') }}
@endcomponent

@endcomponent
