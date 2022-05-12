<tr>
    <td class="markup-links">
        @if ($row->type === \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT)
            <a class="break-words" href="{{ route('mailcoach.emailLists.tags.edit', [$emailList, $row]) }}">
                {{ $row->name }}
            </a>
        @else
            {{ $row->name }}
        @endif
    </td>
    <td class="td-numeric">{{ $row->subscriber_count }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->updated_at->toMailcoachFormat() }}</td>

    <td class="td-action">
        <x-mailcoach::confirm-button
            onConfirm="() => $wire.deleteTag({{ $row->id }})"
            :confirm-text="__('mailcoach - Are you sure you want to delete tag :tagName?', ['tagName' => $row->name])"
            class="icon-button hover:text-red-500"
        >
            <i class="far fa-trash-alt"></i>
        </x-mailcoach::confirm-button>
    </td>
</tr>
