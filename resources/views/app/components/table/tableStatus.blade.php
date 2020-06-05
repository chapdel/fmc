<p class="table-status">
    {{ __('Displaying') }} {{ $paginator->count() }} {{ __('of') }} {{ $totalCount }} {{ Illuminate\Support\Str::plural($name) }}.
    @if($paginator->total() !== $totalCount)
        <a href="{{ $showAllUrl }}" class="link-dimmed" data-turbolinks="false">
            {{ __('Show all') }}
        </a>
    @endif
</p>

{{ $paginator->appends(request()->input())->links() }}
