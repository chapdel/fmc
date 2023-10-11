<?php
/** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
?>
@props([
    'campaign',
    'status',
    'type' => 'help',
    'sync' => false,
    'cancelable' => false,
    'progress' => null,
    'progressClass' => '',
])

<x-dynamic-component :component="'mailcoach::' . $type" class="shadow" :sync="$sync" full>
    <div class="flex justify-between items-center w-full">
        <div>
            {{ __mc('Campaign') }}
            @if ($campaign->isSent())
                <strong><a target="_blank" href="{{ $campaign->webviewUrl() }}"><strong>{{ $campaign->name }}</strong></a></strong>
            @else
                <strong>{{ $campaign->name }}</strong>
            @endif
            {!! $status !!}

            @if($campaign->emailList)
                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
            @else
                &lt;{{ __mc('deleted list') }}&gt;
            @endif

            @if($campaign->usesSegment())
                ({{ $campaign->segment_description }})
            @endif
        </div>

        @if ($cancelable)
            <x-mailcoach::confirm-button
                class="ml-auto text-red-500 underline"
                onConfirm="() => $wire.cancelSending()"
                :confirm-text="__mc('Are you sure you want to cancel sending this campaign?')">
                Cancel
            </x-mailcoach::confirm-button>
        @endif
    </div>
    @if (! is_null($progress))
        <div class="progress-bar mt-6">
            <div class="progress-bar-value {{ $progressClass }}" style="width: {{ $progress }}%"></div>
        </div>
    @endif
</x-dynamic-component>
