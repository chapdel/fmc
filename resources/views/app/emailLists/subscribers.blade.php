@extends('mailcoach::app.emailLists.layouts.edit', ['emailList' => $emailList])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $emailList->name }}</span></li>
@endsection

@section('emailList')
    <div class="table-actions">
        <div class=buttons>
            <button class="button" data-modal-trigger="create-subscriber">
                <c-icon-label icon="fa-user" text="Add subscriber"/>
            </button>

            <c-modal title="Create subscriber" name="create-subscriber" :open="$errors->any()">
                @include('mailcoach::app.emailLists.subscriber.partials.create')
            </c-modal>

            <div class="dropdown" data-dropdown>
                <button class="button" data-dropdown-trigger>
                    <span class="icon-button">
                        <i class="fas fa-ellipsis-v | dropdown-trigger-rotate"></i>
                    </span>
                </button>
                <ul class="dropdown-list dropdown-list-right | hidden" data-dropdown-list>
                    <li>
                        <a href="{{route('mailcoach.emailLists.import-subscribers', $emailList)}}">
                            <c-icon-label icon="fa-cloud-upload-alt" text="Import subscribers"/>
                        </a>
                    </li>
                    @if($subscribers->count() > 0)
                        <li>
                            <c-form-button
                                :action="route('mailcoach.emailLists.subscribers.export', $emailList) . '?' . request()->getQueryString()">
                                <c-icon-label icon="fa-file"
                                              :text="$emailList->allSubscribers()->count() === $subscribers->total() ? 'Export all subscribers' : 'Export ' . $subscribers->total() . ' ' . \Illuminate\Support\Str::plural('subscriber', $subscribers->total())"/>
                            </c-form-button>
                        </li>
                        <li>
                            <c-form-button
                                :action="route('mailcoach.emailLists.destroy-unsubscribes', $emailList)"
                                method="DELETE" data-confirm="true">
                                <c-icon-label icon="fa-trash-alt" text="Delete unsubscribes" :caution="true"/>
                            </c-form-button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        @if($emailList->allSubscribers()->count())
            <div class="table-filters">
                <c-filters>
                    <c-context :queryString="$queryString" attribute="status">
                        <c-filter active-on="">
                            All
                            <c-counter :number="$emailList->allSubscribers()->count()"/>
                        </c-filter>
                        <c-filter active-on="unconfirmed">
                            Unconfirmed
                            <c-counter :number="$emailList->allSubscribers()->unconfirmed()->count()"/>
                        </c-filter>
                        <c-filter active-on="subscribed">
                            Subscribed
                            <c-counter :number="$emailList->allSubscribers()->subscribed()->count()"/>
                        </c-filter>
                        <c-filter active-on="unsubscribed">
                            Unsubscribed
                            <c-counter :number="$emailList->allSubscribers()->unsubscribed()->count()"/>
                        </c-filter>
                    </c-context>
                </c-filters>
                <c-search placeholder="Filter subscribers…"/>
            </div>
        @endif
    </div>

    @if($emailList->allSubscribers()->count())
        <table class="table table-fixed">
            <thead>
            <tr>
                <th class="w-4"></th>
                <c-th sort-by="email">Email</c-th>
                <th class="hidden | md:table-cell">Tags</th>
                @if(request()->input('filter.status') === \Spatie\Mailcoach\Enums\SubscriptionStatus::UNSUBSCRIBED)
                    <c-th sort-by="-unsubscribed_at" class="w-48 th-numeric hidden | md:table-cell">Unsubscribed at</c-th>
                @else
                    <c-th sort-by="-created_at" class="w-48 th-numeric hidden | md:table-cell">Subscribed at</c-th>
                @endif

                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscribers as $subscriber)
                <tr>
                    <td>
                        @if ($subscriber->isUnconfirmed())
                            <i class="fas fa-question-circle text-orange-500" title="Unconfirmed"></i>
                        @endif
                        @if ($subscriber->isSubscribed())
                            <i class="fas fa-check text-green-500" title="Subscribed"></i>
                        @endif
                        @if ($subscriber->isUnsubscribed())
                            <i class="fas fa-ban text-gray-400" title="Unsubscribed"></i>
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
                    <td class="hidden | md:table-cell">
                        @foreach($subscriber->tags()->pluck('name') as $tag)
                            <span class=tag>{{ $tag }}</span>
                        @endforeach
                    </td>
                    <td class="td-numeric hidden | md:table-cell">{{
    $subscriber->isUnsubscribed()
    ? $subscriber->unsubscribed_at->toMailcoachFormat()
    : $subscriber->created_at->toMailcoachFormat() }}</td>
                    <td class="td-action">
                        <div class="dropdown" data-dropdown>
                            <button class="icon-button" data-dropdown-trigger>
                                <i class="fas fa-ellipsis-v | dropdown-trigger-rotate"></i>
                            </button>
                            <ul class="dropdown-list dropdown-list-left | hidden" data-dropdown-list>
                                @if ($subscriber->isUnconfirmed())
                                    <li>
                                        <c-form-button
                                            :action="route('mailcoach.subscriber.resend-confirmation-mail', [$subscriber])"
                                            method="POST" data-confirm="true">
                                            <c-icon-label icon="fa-envelope" text="Resend confirmation mail"/>
                                        </c-form-button>
                                    </li>
                                    <li>
                                        <c-form-button
                                            :action="route('mailcoach.subscriber.confirm', [$subscriber])"
                                            method="POST" data-confirm="true">
                                            <c-icon-label icon="fa-check" text="Confirm"/>
                                        </c-form-button>
                                    </li>
                                @endif
                                @if ($subscriber->isSubscribed())
                                    <li>
                                        <c-form-button
                                            :action="route('mailcoach.subscriber.unsubscribe', [$subscriber])"
                                            method="POST" data-confirm="true">
                                            <c-icon-label icon="fa-ban" text="Unsubscribe"/>
                                        </c-form-button>
                                    </li>
                                @endif
                                @if ($subscriber->isUnsubscribed())
                                    <li>
                                        <c-form-button
                                            :action="route('mailcoach.subscriber.resubscribe', [$subscriber])"
                                            method="POST" data-confirm="true">
                                            <c-icon-label icon="fa-redo" text="Resubscribe"/>
                                        </c-form-button>
                                    </li>
                                @endif
                                <li>
                                    <c-form-button
                                        :action="route('mailcoach.emailLists.subscriber.delete', [$subscriber->emailList, $subscriber])"
                                        method="DELETE" data-confirm="true">
                                        <c-icon-label icon="fa-trash-alt" text="Delete" :caution="true"/>
                                    </c-form-button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <c-table-status name="subscriber" :paginator="$subscribers" :total-count="$totalSubscriptionsCount"
                        :show-all-url="route('mailcoach.campaigns')">
        </c-table-status>
    @else
        <p class="alert alert-info">
            So where is everyone? This list is empty.
        </p>
    @endif
@endsection
