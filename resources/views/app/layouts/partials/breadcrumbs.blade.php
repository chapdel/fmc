<div class="h-12 flex items-center gap-x-4 px-4 text-gray-700 text-sm">
    <a class="font-semibold" href="{{ route(config('mailcoach.redirect_home')) }}"><i class="fa fa-home"></i></a>
    @foreach (app($breadcrumbsNavigationClass ?? Spatie\Mailcoach\MainNavigation::class)->breadcrumbs() as $breadcrumb)
        @if ($loop->first)
            <span>&gt;</span>
        @endif
        <a class="font-semibold" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
        @if (! $loop->last)
            <span>&gt;</span>
        @endif
    @endforeach
    @if (isset($title) && $title !== ($breadcrumb['title'] ?? ''))
        <span>&gt;</span>
        <span>
            <span class="">{{ $title }}</span>
        </span>
    @endif
</div>
