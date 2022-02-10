@if($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::DRAFT)
    @if($campaign->scheduled_at)
        <i title="{{ __('mailcoach - Scheduled') }}" class="far fa-clock text-orange-500" />
    @else
        <i title="{{ __('mailcoach - Draft') }}" class="far fa-edit text-gray-500" />
    @endif
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::SENT)
    <i title="{{ __('mailcoach - Sent') }}" class="fas fa-check text-green-500" />
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::SENDING)
    <i title="{{ __('mailcoach - Sending') }}" class="fas fa-sync fa-spin text-blue-500" />
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::CANCELLED)
    <i title="{{ __('mailcoach - Cancelled') }}" class="fas fa-ban text-red-500" />
@endif
