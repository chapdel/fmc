<div class="pl-1 flex items-center gap-x-2 text-xs text-indigo-900/70">
    <a class="hover:text-blue-800" href="{{ route(config('mailcoach.redirect_home')) }}">
        Home
    </a>
    @foreach (app($breadcrumbsNavigationClass ?? Spatie\Mailcoach\MainNavigation::class)->breadcrumbs() as $breadcrumb)
        <i class="fa fa-angle-right text-gray-400"></i>
        <a class="hover:text-blue-800 last:font-semibold min-w-0 truncate" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
    @endforeach

    @if (isset($title) && $title !== ($breadcrumb['title'] ?? ''))
        <i class="fa fa-angle-right text-gray-400"></i>
        <span class="font-semibold min-w-0 truncate">{{ $title }}</span>
    @endif
</div>
