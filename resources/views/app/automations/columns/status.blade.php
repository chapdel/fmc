@php($automation = $getRecord())

<div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm pl-5">
    <button class="group" wire:click.prevent="toggleAutomationStatus({{ $automation->id }})">
        @if($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::Paused)
            <span class="group-hover:opacity-0 fas fa-magic text-gray-400"></span>
            <span title="{{ __mc('Start Automation') }}" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100">
                <x-mailcoach::rounded-icon class="w-5 h-5 relative -top-[2px]" type="success" icon="fas fa-play"/>
            </span>
        @else
            <span class="group-hover:opacity-0 fas fa-sync fa-spin text-green-500"></span>
            <span title="{{ __mc('Pause Automation') }}"
                  class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100">
                <x-mailcoach::rounded-icon class="w-5 h-5 relative -top-[2px]" type="warning" icon="fas fa-pause"/>
            </span>
        @endif
    </button>
</div>
