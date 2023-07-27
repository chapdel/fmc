@php($emailList = $getRecord())

<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm px-3">
    <livewire:mailcoach::email-list-count lazy wire:key="{{ $emailList->id }}" :email-list="$emailList" />
</div>
