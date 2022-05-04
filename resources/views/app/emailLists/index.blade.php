<x-mailcoach::layout-main :title="__('mailcoach - Lists')">
    <div class="table-actions">
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getEmailListClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-list')" icon="fa-address-book" :label="__('mailcoach - Create list')" />

            <x-mailcoach::modal :title="__('mailcoach - Create list')" name="create-list">
                <livewire:mailcoach::create-list />
            </x-mailcoach::modal>
        @endcan

        @if($emailLists->count() || $searching)
            <div class="table-filters">
                <x-mailcoach::search :placeholder="__('mailcoach - Filter lists…')"/>
            </div>
        @endif
    </div>

    @if($emailLists->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                <x-mailcoach::th sort-by="name" sort-default>{{ __('mailcoach - Name') }}</x-mailcoach::th>
                <x-mailcoach::th sort-by="-active_subscribers_count" class="w-32 th-numeric">{{ __('mailcoach - Active') }}</x-mailcoach::th>
                <x-mailcoach::th sort-by="-created_at" class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Created') }}</x-mailcoach::th>
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($emailLists as $emailList)
                <tr>
                    <td class="markup-links">
                        <a class="break-words" href="{{ route('mailcoach.emailLists.summary', $emailList) }}">
                            {{ $emailList->name }}
                        </a>
                    </td>
                    <td class="td-numeric">{{ number_format($emailList->active_subscribers_count) }}</td>
                    <td class="td-numeric hidden | xl:table-cell">
                        {{ $emailList->created_at->toMailcoachFormat() }}
                    </td>
                    <td class="td-action">
                        <x-mailcoach::form-button
                            :action="route('mailcoach.emailLists.delete', $emailList)"
                            method="DELETE"
                            data-confirm="true"
                            :data-confirm-text="__('mailcoach - Are you sure you want to delete list :emailListName?', ['emailListName' => $emailList->name])"
                            class="icon-button hover:text-red-500"
                        >
                            <i class="far fa-trash-alt"></i>
                        </x-mailcoach::form-button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <x-mailcoach::table-status
            :name="__('mailcoach - list|lists')"
            :paginator="$emailLists"
            :total-count="$totalEmailListsCount"
            :show-all-url="route('mailcoach.emailLists')"
        ></x-mailcoach::table-status>

    @else
        <x-mailcoach::help>
            @if ($searching)
                {{ __('mailcoach - No email lists found.') }}
            @else
                {{ __("mailcoach - You'll need at least one list to gather subscribers.") }}
            @endif
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-main>
