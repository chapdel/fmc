<x-mailcoach::layout-list :title="__('mailcoach - Subscribers')" :emailList="$emailList">
    <div class="table-actions">
        <div class=buttons>
            <x-mailcoach::button type="button" x-on:click="$store.modals.open('create-subscriber')" :label="__('mailcoach - Add subscriber')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create subscriber')" name="create-subscriber" :open="$errors->any()">
                @include('mailcoach::app.emailLists.subscribers.partials.create')
            </x-mailcoach::modal>

            <x-mailcoach::dropdown direction="right" triggerClass="button">
                <ul>
                    <li>
                        <a href="{{route('mailcoach.emailLists.import-subscribers', $emailList)}}">
                            <x-mailcoach::icon-label icon="fa-fw fas fa-cloud-upload-alt" :text="__('mailcoach - Import subscribers')"/>
                        </a>
                    </li>
                    @if($subscribers->count() > 0)
                        <li>
                            <x-mailcoach::form-button
                                data-turbo="false"
                                :action="route('mailcoach.emailLists.subscribers.export', $emailList) . '?' . request()->getQueryString()">

                                @if($allSubscriptionsCount === $subscribers->total())
                                    <x-mailcoach::icon-label icon="fa-fw fas fa-file" :text="__('mailcoach - Export all subscribers')"/>
                                @else
                                    <x-mailcoach::icon-label icon="fa-fw fas fa-file" :text="__('mailcoach - Export :total :subscriber', ['total' => $subscribers->total(), 'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $subscribers->total())])"/>
                                @endif
                            </x-mailcoach::form-button>
                        </li>
                        <li>
                            <x-mailcoach::confirm-button
                                :action="route('mailcoach.emailLists.destroy-unsubscribes', $emailList)"
                                method="DELETE" :confirm-text="__('mailcoach - Are you sure you want to delete unsubscribes in :emailList?', ['emailList' => $emailList->name])">
                                <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__('mailcoach - Delete unsubscribes')" :caution="true"/>
                            </x-mailcoach::confirm-button>
                        </li>
                    @endif
                </ul>
            </x-mailcoach::dropdown>
        </div>

        @if($allSubscriptionsCount)
            <div class="table-filters">
                <x-mailcoach::filters>
                    <x-mailcoach::filter :queryString="$queryString" attribute="status" active-on="">
                        {{ __('mailcoach - All') }}
                        <x-mailcoach::counter :number="$allSubscriptionsCount"/>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :queryString="$queryString" attribute="status" active-on="unconfirmed">
                        {{ __('mailcoach - Unconfirmed') }}
                        <x-mailcoach::counter :number="$unconfirmedCount"/>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :queryString="$queryString" attribute="status" active-on="subscribed">
                        {{ __('mailcoach - Subscribed') }}
                        <x-mailcoach::counter :number="$totalSubscriptionsCount"/>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :queryString="$queryString" attribute="status" active-on="unsubscribed">
                        {{ __('mailcoach - Unsubscribed') }}
                        <x-mailcoach::counter :number="$unsubscribedCount"/>
                    </x-mailcoach::filter>
                </x-mailcoach::filters>
                <x-mailcoach::search :placeholder="__('mailcoach - Filter subscribers…')"/>
            </div>
        @endif
    </div>

    @if($allSubscriptionsCount)
        <table class="table table-fixed">
            <thead>
            <tr>
                <th class="w-4"></th>
                <x-mailcoach::th sort-by="email">{{ __('mailcoach - Email') }}</x-mailcoach::th>
                <th class="hidden | xl:table-cell">{{ __('mailcoach - Tags') }}</th>
                @if(request()->input('filter.status') === \Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus::UNSUBSCRIBED)
                    <x-mailcoach::th sort-by="-unsubscribed_at" class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Unsubscribed at') }}</x-mailcoach::th>
                @else
                    <x-mailcoach::th sort-by="-created_at" class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Subscribed at') }}</x-mailcoach::th>
                @endif

                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscribers as $subscriber)
                <tr class="markup-links">
                    <td>
                        @if ($subscriber->isUnconfirmed())
                            <i class="far fa-question-circle text-orange-500" title="{{ __('mailcoach - Unconfirmed') }}"></i>
                        @endif
                        @if ($subscriber->isSubscribed())
                            <i class="fas fa-check text-green-500" title="{{ __('mailcoach - Subscribed') }}"></i>
                        @endif
                        @if ($subscriber->isUnsubscribed())
                            <i class="fas fa-ban text-gray-400" title="{{ __('mailcoach - Unsubscribed') }}"></i>
                        @endif
                    </td>
                    <td>
                        <a class="break-words"
                           href="{{ route('mailcoach.emailLists.subscriber.details', [$subscriber->emailList, $subscriber]) }}">
                            {{ $subscriber->email }}
                        </a>
                        <div class="td-secondary-line">
                            {{ $subscriber->first_name }} {{ $subscriber->last_name }}
                        </div>
                    </td>
                    <td class="hidden | xl:table-cell">
                        @foreach($subscriber->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT) as $tag)
                            @include('mailcoach::app.partials.tag')
                        @endforeach
                    </td>
                    <td class="td-numeric hidden | xl:table-cell">{{
    $subscriber->isUnsubscribed()
    ? $subscriber->unsubscribed_at?->toMailcoachFormat()
    : $subscriber->created_at?->toMailcoachFormat() }}</td>
                    <td class="td-action">
                        <x-mailcoach::dropdown direction="left">
                            <ul>
                                @if ($subscriber->isUnconfirmed())
                                    <li>
                                        <x-mailcoach::confirm-button
                                            :action="route('mailcoach.subscriber.resend-confirmation-mail', [$subscriber])"
                                            method="POST" :confirm-text="__('mailcoach - Are you sure you want to resend the confirmation mail :email?', ['email' => $subscriber->email])">
                                            <x-mailcoach::icon-label icon="fa-fw far fa-envelope" :text="__('mailcoach - Resend confirmation mail')"/>
                                        </x-mailcoach::confirm-button>
                                    </li>
                                    <li>
                                        <x-mailcoach::confirm-button
                                            :action="route('mailcoach.subscriber.confirm', [$subscriber])"
                                            method="POST" :confirm-text="__('mailcoach - Are you sure you want to confirm :email?', ['email' => $subscriber->email])">
                                            <x-mailcoach::icon-label icon="fa-fw fas fa-check" :text="__('mailcoach - Confirm')"/>
                                        </x-mailcoach::confirm-button>
                                    </li>
                                @endif
                                @if ($subscriber->isSubscribed())
                                    <li>
                                        <x-mailcoach::confirm-button
                                            :action="route('mailcoach.subscriber.unsubscribe', [$subscriber])"
                                            method="POST" :confirm-text="__('mailcoach - Are you sure you want to unsubscribe :email?', ['email' => $subscriber->email])">
                                            <x-mailcoach::icon-label icon="fa-fw fas fa-ban" :text="__('mailcoach - Unsubscribe')"/>
                                        </x-mailcoach::confirm-button>
                                    </li>
                                @endif
                                @if ($subscriber->isUnsubscribed())
                                    <li>
                                        <x-mailcoach::confirm-button
                                            :action="route('mailcoach.subscriber.resubscribe', [$subscriber])"
                                            method="POST" :confirm-text="__('mailcoach - Are you sure you want to resubscribe :email?', ['email' => $subscriber->email])">
                                            <x-mailcoach::icon-label icon="fa-fw fas fa-redo" :text="__('mailcoach - Resubscribe')"/>
                                        </x-mailcoach::confirm-button>
                                    </li>
                                @endif
                                <li>
                                    <x-mailcoach::confirm-button
                                        :action="route('mailcoach.emailLists.subscriber.delete', [$subscriber->emailList, $subscriber])"
                                        method="DELETE" :confirm-text="__('mailcoach - Are you sure you want to delete subscriber :email?', ['email' => $subscriber->email])">
                                        <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true"/>
                                    </x-mailcoach::confirm-button>
                                </li>
                            </ul>
                        </x-mailcoach::dropdown>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <x-mailcoach::table-status :name="__('mailcoach - subscriber|subscribers')" :paginator="$subscribers" :total-count="$totalSubscriptionsCount"
                        :show-all-url="route('mailcoach.emailLists.subscribers', $emailList)">
        </x-mailcoach::table-status>
    @else
        <x-mailcoach::help>
            {{ __('mailcoach - So where is everyone? This list is empty.') }}
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-list>
