@php($subscribers ??= null)
@php($allSubscriptionsCount ??= null)
<x-mailcoach::data-table
    name="subscriber"
    :rows="$subscribers"
    :totalRowsCount="$allSubscriptionsCount"
    rowPartial="mailcoach::app.emailLists.subscribers.partials.row"
    :emptyText="__('mailcoach - So where is everyone? This list is empty.')"
    :no-results-text="config('mailcoach.encryption.enabled')
        ? __('mailcoach - No subscribers found. Encryption is enabled, so only searches on exact matches before and after the \'@\' symbol are supported. For example: john, doe.com or john@doe.com')
        : __('mailcoach - No subscribers found.')"
    :filters="[
        ['attribute' => 'status', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $allSubscriptionsCount ?? null],
        ['attribute' => 'status', 'value' => 'unconfirmed', 'label' => __('mailcoach - Unconfirmed'), 'count' => $unconfirmedCount ?? null],
        ['attribute' => 'status', 'value' => 'subscribed', 'label' => __('mailcoach - Subscribed'), 'count' => $totalSubscriptionsCount ?? null],
        ['attribute' => 'status', 'value' => 'unsubscribed', 'label' => __('mailcoach - Unsubscribed'), 'count' => $unsubscribedCount ?? null],
    ]"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'email', 'label' => __('mailcoach - Email')],
        ['label' => __('mailcoach - Tags'), 'class' => 'hidden | xl:table-cell'],
        request()->input('filter.status') === \Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus::Unsubscribed
            ? ['attribute' => '-unsubscribed_at', 'label' => __('mailcoach - Unsubscribed at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell']
            : ['attribute' => '-subscribed_at', 'label' => __('mailcoach - Subscribed at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
>
    @slot('actions')
        <div class=buttons>
            <x-mailcoach::button type="button" x-on:click="$store.modals.open('create-subscriber')" :label="__('mailcoach - Add subscriber')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create subscriber')" name="create-subscriber">
                <livewire:mailcoach::create-subscriber :email-list="$emailList" />
            </x-mailcoach::modal>

            <x-mailcoach::dropdown direction="right" triggerClass="-ml-2 rounded-l-none button">
                <ul>
                    <li>
                        <a href="{{route('mailcoach.emailLists.import-subscribers', $emailList)}}">
                            <x-mailcoach::icon-label icon="fa-fw far fa-cloud-upload-alt" :text="__('mailcoach - Import subscribers')"/>
                        </a>
                    </li>
                    @if($subscribers?->count() > 0)
                        <li>
                            <x-mailcoach::form-button
                                :action="route('mailcoach.emailLists.subscribers.export', $emailList) . '?filter[search]=' . ($filter['search'] ?? '') . '&filter[status]=' . ($filter['status'] ?? '')">

                                @if($allSubscriptionsCount === $subscribers->total())
                                    <x-mailcoach::icon-label icon="fa-fw far fa-file" :text="__('mailcoach - Export all subscribers')"/>
                                @else
                                    <x-mailcoach::icon-label icon="fa-fw far fa-file" :text="__('mailcoach - Export :total :subscriber', ['total' => $subscribers->total(), 'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $subscribers->total())])"/>
                                @endif
                            </x-mailcoach::form-button>
                        </li>
                        <li>
                            <x-mailcoach::confirm-button
                                onConfirm="() => $wire.deleteUnsubscribes()"
                                method="DELETE" :confirm-text="__('mailcoach - Are you sure you want to delete unsubscribed emails in :emailList?', ['emailList' => $emailList->name])">
                                <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__('mailcoach - Delete unsubscribed')" :caution="true"/>
                            </x-mailcoach::confirm-button>
                        </li>
                    @endif
                </ul>
            </x-mailcoach::dropdown>
        </div>
    @endslot
</x-mailcoach::data-table>
