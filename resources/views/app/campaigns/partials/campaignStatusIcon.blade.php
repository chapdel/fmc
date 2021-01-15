@if($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::DRAFT)
    @if($campaign->scheduled_at)
        <i title="{{ __('Scheduled') }}" class="far fa-clock text-orange-500" />
    @else
        <i title="{{ __('Draft') }}" class="far fa-edit text-gray-500" />
    @endif
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::SENT)
    <i title="{{ __('Sent') }}" class="far fa-check text-green-500" />
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::SENDING)
    <i title="{{ __('Sending') }}" class="far fa-sync fa-spin text-blue-500" />
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::CANCELLED)
    <i title="{{ __('Cancelled') }}" class="far fa-ban text-red-500" />
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::AUTOMATED)
    <i title="{{ __('Automated') }}" class="far fa-magic text-green-500" />
@endif
