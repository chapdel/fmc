@if($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Draft)
    @if($campaign->scheduled_at)
        <x-mailcoach::rounded-icon size="md" title="{{ __('mailcoach - Scheduled') }}" type="warning" icon="far fa-clock"/>
    @else
        <x-mailcoach::rounded-icon size="md" title="{{ __('mailcoach - Draft') }}" type="neutral" icon="far fa-pen"/>
    @endif
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Sent)
    <x-mailcoach::rounded-icon size="md" title="{{ __('mailcoach - Sent') }}" type="success" icon="far fa-check"/>
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Sending)
    <x-mailcoach::rounded-icon size="md" title="{{ __('mailcoach - Sending') }}" type="info" icon="far fa-sync fa-spin"/>
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Cancelled)
    <x-mailcoach::rounded-icon size="md" title="{{ __('mailcoach - Cancelled') }}" type="error" icon="far ban"/>
@endif
