@if ($tag->type === \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Mailcoach)
    <span class="tag">
        <span class="inline-block w-8 -ml-2 py-1 pl-2 mr-1 rounded-full bg-blue-400">
            @include('mailcoach::app.layouts.partials.logoSvg')
        </span>
        {{ str_replace('mc::', '', $tag->name) }}
    </span>
@else
    <a href="{{ route('mailcoach.emailLists.tags.edit', [$emailList, $tag]) }}" class="{{ $highlight ?? false ? 'tag' : 'tag-neutral' }}">
        {{ $tag->name }}
    </a>
@endif
