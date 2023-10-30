@php($tag = $getRecord())
<div wire:key="population-count-{{ $tag->uuid }}">
    <livewire:mailcoach::tag-population-count lazy wire:key="{{ $tag->uuid }}" :tag="$tag" />
</div>
