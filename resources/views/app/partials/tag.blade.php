@if ($tag->type === \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::MAILCOACH)
    <span class="tag">
        <span class="w-4 h-4 mr-1">@include('mailcoach::app.layouts.partials.logoSvg')</span>
        {{ str_replace('mc::', '', $tag->name) }}
    </span>
@else
    <span class="tag">{{ $tag->name }}</span>
@endif
