<p class="table-status">
    @if($paginator->total() !== $totalCount)
        {{ __('mailcoach - Filtering :resource', [
            'resource' => trans_choice($name, $totalCount)
        ]) }}.
        <a href="{{ $showAllUrl }}" class="link-dimmed" data-turbolinks="false">
            {{ __('mailcoach - Show all') }}
        </a>
    @endif
</p>

{{ $paginator->appends(request()->input())->links('mailcoach::app.components.table.pagination') }}
