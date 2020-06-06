@if($status === 'draft')
    @if($campaign->scheduled_at)
        <i title="{{ __('Scheduled') }}" class="far fa-clock text-orange-500" />
    @else
        <i title="{{ __('Draft') }}" class="far fa-edit text-gray-500" />
    @endif
@elseif ($status === 'sent')
    <i title="{{ __('Sent') }}" class="fas fa-check text-green-500" />
@elseif ($status === 'sending')
    <i title="{{ __('Sending') }}" class="fas fa-sync fa-spin text-blue-500" />
@endif
