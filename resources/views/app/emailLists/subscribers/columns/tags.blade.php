@php($subscriber = $getRecord())

<div class="fi-ta-text-item inline-flex pb-4 items-center gap-1.5 text-sm flex-wrap">
    @foreach($subscriber->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default) as $tag)
        @include('mailcoach::app.partials.tag', [
            'emailList' => $this->emailList,
            'highlight' => $this->tableSearch && str_contains($tag->name, $this->tableSearch),
        ])
    @endforeach
</div>
