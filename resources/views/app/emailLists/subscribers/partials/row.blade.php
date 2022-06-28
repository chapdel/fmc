<tr class="markup-links">
    <td>
        @if ($row->isUnconfirmed())
            <i class="far fa-question-circle text-orange-500" title="{{ __('mailcoach - Unconfirmed') }}"></i>
        @endif
        @if ($row->isSubscribed())
            <i class="fas fa-check text-green-500" title="{{ __('mailcoach - Subscribed') }}"></i>
        @endif
        @if ($row->isUnsubscribed())
            <i class="fas fa-ban text-gray-400" title="{{ __('mailcoach - Unsubscribed') }}"></i>
        @endif
    </td>
    <td>
        <a class="break-words"
           href="{{ route('mailcoach.emailLists.subscriber.details', [$row->emailList, $row]) }}">
            {{ $row->email }}
        </a>
        <div class="td-secondary-line">
            {{ $row->first_name }} {{ $row->last_name }}
        </div>
    </td>
    <td class="hidden | xl:table-cell">
        @foreach($row->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default) as $tag)
            @include('mailcoach::app.partials.tag')
        @endforeach
    </td>
    <td class="td-numeric hidden | xl:table-cell">{{
    $row->isUnsubscribed()
    ? $row->unsubscribed_at?->toMailcoachFormat()
    : $row->created_at?->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                @if ($row->isUnconfirmed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.resendConfirmation({{ $row->id }})"
                            :confirm-text="__('mailcoach - Are you sure you want to resend the confirmation mail :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw far fa-envelope"
                                                     :text="__('mailcoach - Resend confirmation mail')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.confirm({{ $row->id }})"
                            :confirm-text="__('mailcoach - Are you sure you want to confirm :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-check" :text="__('mailcoach - Confirm')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                @if ($row->isSubscribed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.unsubscribe({{ $row->id }})"
                            :confirm-text="__('mailcoach - Are you sure you want to unsubscribe :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-ban" :text="__('mailcoach - Unsubscribe')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                @if ($row->isUnsubscribed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.resubscribe({{ $row->id }})"
                            :confirm-text="__('mailcoach - Are you sure you want to resubscribe :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-redo" :text="__('mailcoach - Resubscribe')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__('mailcoach - Are you sure you want to delete subscriber :email?', ['email' => $row->email])"
                        onConfirm="() => $wire.deleteSubscriber({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__('mailcoach - Delete')"
                                                 :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
