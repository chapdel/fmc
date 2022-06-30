<div class="h-16 flex items-center gap-x-2 text-blue-500 text-sm">
    <a class="hover:underline" href="{{ route(config('mailcoach.redirect_home')) }}">
        Home
    </a>
    @foreach (app($breadcrumbsNavigationClass ?? Spatie\Mailcoach\MainNavigation::class)->breadcrumbs() as $breadcrumb)
        @if ($loop->first)
            <i class="fa fa-angle-right text-blue-200"></i>
        @endif
        <a class="hover:underline" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
        @if (! $loop->last)
            <i class="fa fa-angle-right text-blue-200"></i>
        @endif
    @endforeach
    @if (isset($title) && $title !== ($breadcrumb['title'] ?? ''))
        <i class="fa fa-angle-right text-blue-200"></i>
        <span>
            <span class="">{{ $title }}</span>
        </span>
    @endif
</div>
