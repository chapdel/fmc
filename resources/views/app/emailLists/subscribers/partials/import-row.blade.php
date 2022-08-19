<tr>
    <td>
        @switch($row->status)
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Pending)
                <i title="{{ __('mailcoach - Scheduled') }}" class="far fa-clock text-orange-500`"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Importing)
                <i title="{{ __('mailcoach - Importing') }}"
                   class="fas fa-sync fa-spin text-blue-500"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Completed)
                <i title="{{ __('mailcoach - Completed') }}" class="fas fa-check text-green-500"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Failed)
                <i title="{{ __('mailcoach - Failed') }}" class="fas fa-times text-red-500"></i>
            @break
        @endswitch
    </td>
    <td class="td-numeric">
        {{ $row->created_at->toMailcoachFormat() }}
    </td>
    <td class="td-numeric">{{ $row->subscribers()->count() }}</td>
    <td class="td-numeric">{{ $row->imported_subscribers_count }}</td>
    <td class="td-numeric">{{ count($row->errors ?? []) }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                @if (count($row->errors ?? []))
                    <li>
                        <a href="#"
                           wire:click.prevent="downloadAttatchment('{{ $row->id }}', 'errorReport')"
                           download>
                            <x-mailcoach::icon-label icon="far fa-times-circle"
                                                     :text="__('mailcoach - Error report')"/>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#"
                       wire:click.prevent="downloadAttatchment('{{ $row->id }}', 'importFile')"
                       download>
                        <x-mailcoach::icon-label icon="far fa-file"
                                                 :text="__('mailcoach - Uploaded file')"/>
                    </a>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__('mailcoach - Are you sure you want to delete this import?')"
                        onConfirm="() => $wire.deleteImport({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__('mailcoach - Delete')"
                                                 :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
