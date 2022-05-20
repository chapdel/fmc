<div>
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'profile')" :active="$tab === 'profile'">
                {{ __('mailcoach - Profile') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'attributes')" :active="$tab === 'attributes'">
                {{ __('mailcoach - Attributes') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'sends')" :active="$tab === 'sends'">
                <x-mailcoach::icon-label :text="__('mailcoach - Received mails')" invers :count="$totalSendsCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @if ($tab === 'profile')
        <form
            class="form-grid"
            method="POST"
            wire:submit.prevent="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__('mailcoach - Email')" name="email" wire:model="subscriber.email" type="email" required />
            <x-mailcoach::text-field :label="__('mailcoach - First name')" name="first_name" wire:model="subscriber.first_name" />
            <x-mailcoach::text-field :label="__('mailcoach - Last name')" name="last_name" wire:model="subscriber.last_name" />
            <x-mailcoach::tags-field
                :label="__('mailcoach - Tags')"
                name="tags"
                :value="$tags"
                :tags="$subscriber->emailList->tags()->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT)->pluck('name')->toArray()"
                allow-create
            />

            <div class="form-buttons">
                <x-mailcoach::button :label="__('mailcoach - Save subscriber')" />
            </div>
        </form>
    @endif

    @if ($tab === 'attributes')
        @if($subscriber->extra_attributes->count())
            <table class="table table-fixed">
                <thead>
                <tr>
                    <th>{{ __('mailcoach - Key') }}</th>
                    <th>{{ __('mailcoach - Value') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subscriber->extra_attributes->all() as $key => $attribute)
                    <tr>
                        <td class="markup-links">
                            {{ $key }}
                        </td>
                        <td class="td-secondary-line">
                            @if(is_array($attribute))
                                <pre>{{ json_encode($attribute, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $attribute }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <x-mailcoach::help>
                {{ __("mailcoach - This user doesn't have any attributes yet.") }}
            </x-mailcoach::help>
        @endif
    @endif

    @if ($tab === 'sends')
        <livewire:mailcoach::subscriber-sends :subscriber="$subscriber" :email-list="$emailList" />
    @endif
</div>
