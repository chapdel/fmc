<p class="table-status">
    @if($paginator->total() !== $totalCount)
        {{ __('mailcoach - Filtering :resource', [
            'resource' => trans_choice($name, $totalCount)
        ]) }}.
        <a href="#" {{ $attributes->wire('click') }} class="link-dimmed" data-turbo="false">
            {{ __('mailcoach - Show all') }}
        </a>
    @endif
</p>

{{ $paginator->links('mailcoach::app.components.table.pagination') }}
