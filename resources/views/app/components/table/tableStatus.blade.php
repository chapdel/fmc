<p class="table-status empty:hidden">
    @if($paginator->total() !== $totalCount)
        {{ __('mailcoach - Filtering :resource', [
            'resource' => \Illuminate\Support\Str::plural($name),
        ]) }}.
        <a href="#" {{ $attributes->wire('click') }} class="link-dimmed" data-turbo="false">
            {{ __('mailcoach - Show all') }}
        </a>
    @endif
</p>

{{ $paginator->links('mailcoach::app.components.table.pagination') }}
