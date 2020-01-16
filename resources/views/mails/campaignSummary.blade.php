@component('mail::message')
Hi,

Campaign **{{ $campaign->name }}** was sent to **{{ $campaign->sent_to_number_of_subscribers ?? 0 }}** subscribers (list {{ $campaign->emailList->name  }}) on {{ $campaign->sent_at->toMailcoachFormat() }}.

<table class="stats">
<tr>
@if ($campaign->track_opens)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.opens', $campaign),
'stat' => $campaign->open_count,
'label' => 'Opens',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->unique_open_count,
'label' => 'Unique Opens',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->open_rate,
'suffix' => '%',
'label' => 'Open Rate',
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">Opens not tracked</div>
</td>
@endif
</tr>

<tr>
@if($campaign->track_clicks)
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.clicks', $campaign),
'stat' => $campaign->click_count,
'label' => 'Clicks',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->unique_click_count,
'label' => 'Unique Clicks',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->click_rate,
'suffix' => '%',
'label' => 'Clicks Rate',
])
</td>
@else
<td colspan=3>
<div class="text-4xl font-semibold">–</div>
<div class="text-sm">Clicks not tracked</div>
</td>
@endif
</tr>

<tr>
<td>
@include('mailcoach::mails.partials.statistic', [
'href' => route('mailcoach.campaigns.unsubscribes', $campaign),
'stat' => $campaign->unsubscribe_count,
'label' => 'Unsubscribes',
])
</td>
<td>
@include('mailcoach::mails.partials.statistic', [
'stat' => $campaign->unsubscribe_rate,
'label' => 'Unsubscribe Rate',
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
'stat' => $campaign->bounce_rate,
'label' => 'Bounce Rate',
'suffix' => '%'
])
</td>
<td></td>
</tr>
</table>

@component('mail::button', ['url' => $summaryUrl])
View summary
@endcomponent

@endcomponent
