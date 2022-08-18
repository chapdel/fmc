<div class="flex flex-wrap items-center gap-x-6 gap-y-2">
    @if($paginator->total() == $totalCount)
    <p class="table-status whitespace-nowrap">
            {{ __('mailcoach - Filtering :resource', [
                'resource' => \Illuminate\Support\Str::plural($name),
            ]) }}.
            <a href="#" {{ $attributes->wire('click') }} class="link-dimmed" data-turbo="false">
                {{ __('mailcoach - Show all') }}
            </a>
        </p>
    @endif
    <div class="flex-grow">
        {{ $paginator->links('mailcoach::app.components.table.pagination') }}
    </div>
</div>
