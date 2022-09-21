<a href="{{ $emailList->websiteUrl() }}">

    <div class="flex justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $emailList->website_title }}</h1>
        </div>
        <div>
            @isset($campaign)
                <a class="font-semibold" href="{{ $emailList->websiteUrl() }}">
                    Past editions
                </a>
            @endisset
        </div>
    </div>
</a>
@if ($emailList->website_intro)
    {{ $emailList->website_intro }}
@endif

@if ($imageUrl = $emailList->websiteHeaderImageUrl())
    <div class="mt-8">
        <img alt="header image" src="{{ $imageUrl }}" />
    </div>
@endif

<div class="mt-20">
    @if($emailList->show_subscription_form_on_website)
        @include('mailcoach::emailListWebsite.partials.subscription')
    @endif
</div>
