@php($subscribers ??= null)
@php($allSubscriptionsCount ??= null)
<x-mailcoach::data-table
    name="subscriber"
    :rows="$subscribers"
    :totalRowsCount="$allSubscriptionsCount"
    rowPartial="mailcoach::app.emailLists.subscribers.partials.row"
    :emptyText="__mc('So where is everyone? This list is empty.')"
    :no-results-text="config('mailcoach.encryption.enabled')
        ? __mc('No subscribers found. Encryption is enabled, so only searches on exact matches before and after the \'@\' symbol are supported. For example: john, doe.com or john@doe.com')
        : __mc('No subscribers found.')"
    :filters="[
        ['attribute' => 'status', 'value' => '', 'label' => __mc('All'), 'count' => $allSubscriptionsCount ?? null],
        ['attribute' => 'status', 'value' => 'unconfirmed', 'label' => __mc('Unconfirmed'), 'count' => $unconfirmedCount ?? null],
        ['attribute' => 'status', 'value' => 'subscribed', 'label' => __mc('Subscribed'), 'count' => $totalSubscriptionsCount ?? null],
        ['attribute' => 'status', 'value' => 'unsubscribed', 'label' => __mc('Unsubscribed'), 'count' => $unsubscribedCount ?? null],
    ]"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'email', 'label' => __mc('Email')],
        ['label' => __mc('Tags'), 'class' => 'hidden | xl:table-cell'],
        request()->input('filter.status') === \Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus::Unsubscribed
            ? ['attribute' => '-unsubscribed_at', 'label' => __mc('Unsubscribed at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell']
            : ['attribute' => '-subscribed_at', 'label' => __mc('Subscribed at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    :bulkActions="[
        ['label' => __mc('Delete :count subscriber|Delete :count subscribers'), 'method' => 'deleteSubscribers'],
        ['label' => __mc('Unsubscribe :count subscriber|Unsubscribe :count subscribers'), 'method' => 'unsubscribeSubscribers'],
    ]"
    :show-filters="count(array_filter(explode(',', $this->tags))) > 0"
    selectable
>
    @slot('actions')
        <div class="buttons flex">
            <x-mailcoach::button type="button" x-on:click="$store.modals.open('create-subscriber')" :label="__mc('Add subscriber')"/>

            <x-mailcoach::modal :title="__mc('Create subscriber')" name="create-subscriber">
                <livewire:mailcoach::create-subscriber :email-list="$emailList" />
            </x-mailcoach::modal>

            <x-mailcoach::dropdown direction="right" triggerClass="-ml-2 rounded-l-none button">
                <ul>
                    <li>
                        <a href="{{route('mailcoach.emailLists.import-subscribers', $emailList)}}">
                            <x-mailcoach::icon-label icon="fa-fw far fa-cloud-upload-alt" :text="__mc('Import subscribers')"/>
                        </a>
                    </li>
                    @if($subscribers?->count() > 0)
                        <li>
                            <x-mailcoach::form-button
                                :action="route('mailcoach.emailLists.subscribers.export', $emailList) . '?filter[search]=' . ($search ?? '') . '&filter[status]=' . ($status ?? '')">

                                @if($allSubscriptionsCount === $subscribers->total())
                                    <x-mailcoach::icon-label icon="fa-fw far fa-file" :text="__mc('Export all subscribers')"/>
                                @else
                                    <x-mailcoach::icon-label icon="fa-fw far fa-file" :text="__mc('Export :total :subscriber', ['total' => $subscribers->total(), 'subscriber' => __mc_choice('subscriber|subscribers', $subscribers->total())])"/>
                                @endif
                            </x-mailcoach::form-button>
                        </li>
                        <li>
                            <x-mailcoach::confirm-button
                                onConfirm="() => $wire.deleteUnsubscribes()"
                                method="DELETE" :confirm-text="__mc('Are you sure you want to delete unsubscribed emails in :emailList?', ['emailList' => $emailList->name])">
                                <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__mc('Delete unsubscribed')" :caution="true"/>
                            </x-mailcoach::confirm-button>
                        </li>
                    @endif
                </ul>
            </x-mailcoach::dropdown>
        </div>
    @endslot

    @slot('filterSlot')
        <div class="flex items-center h-full gap-x-2">
            @php($allTags = $emailList->tags()->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default)->sortBy('name')->get())
            @php($currentFilteredTags = array_filter(explode(',', $tags)))

            @if (count($currentFilteredTags) > 0)
                <x-mailcoach::filters class="gap-x-1">
                    <x-mailcoach::filter active-class="underline" :current="$this->tagType" value="any" attribute="tagType">
                        Any
                    </x-mailcoach::filter>
                    <span>/</span>
                    <x-mailcoach::filter active-class="underline" :current="$this->tagType" value="all" attribute="tagType">
                        All
                    </x-mailcoach::filter>
                </x-mailcoach::filters>
            @endif

            <div>
                @foreach ($currentFilteredTags as $tag)
                    <span class="tag-neutral inline-flex items-center gap-x-1 m-0">
                        <span>tag: {{ $allTags->firstWhere('uuid', $tag)->name  }}</span>

                        <a href="#" wire:click.prevent="removeTagFilter('{{ $tag }}')"><i class="fas fa-times"></i></a>
                    </span>
                @endforeach
            </div>

            @php($availableTags = $allTags->filter(fn ($tag) => !in_array($tag->uuid, $currentFilteredTags)))

            @if (count($availableTags))
                <x-mailcoach::dropdown>
                    <x-slot:trigger>
                        @if (count ($currentFilteredTags) > 0)
                            + {{ __mc('Add tag') }}
                        @else
                            + {{ __mc('Filter on tags') }}
                        @endif
                    </x-slot:trigger>

                    <ul class="overflow-y-scroll w-[max-content] max-w-[20rem]">
                        @foreach ($availableTags as $tag)
                            <li>
                                <a href="#" wire:click.prevent="addTagFilter('{{ $tag->uuid }}')">{{ $tag->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </x-mailcoach::dropdown>
            @endif
        </div>
    @endslot
</x-mailcoach::data-table>
