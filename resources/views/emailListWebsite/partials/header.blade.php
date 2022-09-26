<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>
<header class="relative">
    <div class="absolute inset-0 select-none pointer-events-none opacity-10" style="background: {{ $emailList->getWebsitePrimaryColor() }}"></div>

    <div class="w-full max-w-7xl mx-auto py-16 px-8">
        <div class="flex justify-between">
            <div>
                <a href="{{ $emailList->websiteUrl() }}">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">{{ $emailList->website_title }}</h1>
                    </div>
                </a>
                @if ($emailList->website_intro)
                    <div class="relative markup markup-lists markup-links">
                        {{ app(\Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction::class)->execute($emailList->website_intro) }}
                    </div>
                @endif
                <div>
                    @isset($campaign)
                        <a class="font-semibold" href="{{ $emailList->websiteUrl() }}">
                            &larr; Past editions
                        </a>
                    @endisset
                </div>

                <div class="mt-20">
                    @if($emailList->show_subscription_form_on_website)
                        @include('mailcoach::emailListWebsite.partials.subscription')
                    @endif
                </div>
            </div>

            @if ($imageUrl = $emailList->websiteHeaderImageUrl())
                <div class="w-full max-w-sm">
                    <img class="w-full h-auto" alt="header image" src="{{ $imageUrl }}" />
                </div>
            @endif
        </div>
    </div>
</header>
