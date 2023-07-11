<tr>
    <td class="markup-links">
        <div class="flex items-center">
            <a class="break-words" href="{{ route('mailcoach.emailLists.summary', $row) }}">
                {{ $row->name }}
            </a>
            @if ($row->websiteEnabled())
                <a class="link text-xs ml-2" title="{{ __mc('Website') }}" href="{{ $row->websiteUrl() }}" target="_blank">
                    <i class="fas fa-external-link"></i>
                </a>
            @endif
        </div>
    </td>
    <td class="td-numeric">
        <livewire:mailcoach::email-list-count lazy wire:key="{{ $row->id }}" :email-list="$row" />
    </td>
    <td class="td-numeric hidden | xl:table-cell">
        {{ $row->created_at->toMailcoachFormat() }}
    </td>
    <td class="td-action">
        <x-mailcoach::confirm-button
            :confirm-text="__mc('Are you sure you want to delete list :emailListName?', ['emailListName' => $row->name])"
            onConfirm="() => $wire.deleteList({{ $row->id }})"
            class="icon-button text-red-500 hover:text-red-700"
        >
            <i class="far fa-trash-alt"></i>
        </x-mailcoach::confirm-button>
    </td>
</tr>
