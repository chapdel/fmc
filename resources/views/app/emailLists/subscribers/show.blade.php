<div>
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'profile')" :active="$tab === 'profile'">
                {{ __mc('Profile') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'attributes')"
                                          :active="$tab === 'attributes'">
                {{ __mc('Attributes') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'sends')" :active="$tab === 'sends'">
                <x-mailcoach::icon-label :text="__mc('Received mails')" invers :count="$totalSendsCount"/>
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @if ($tab === 'profile')
        <x-mailcoach::card>
            <form
                    class="form-grid"
                    method="POST"
                    wire:submit="save"
                    @keydown.prevent.window.cmd.s="$wire.call('save')"
                    @keydown.prevent.window.ctrl.s="$wire.call('save')"
            >
                @csrf
                @method('PUT')

                <x-mailcoach::text-field :label="__mc('Email')" name="email" wire:model="email" type="email" required/>
                <x-mailcoach::text-field :label="__mc('First name')" name="first_name" wire:model="first_name"/>
                <x-mailcoach::text-field :label="__mc('Last name')" name="last_name" wire:model="last_name"/>
                <x-mailcoach::tags-field
                        :label="__mc('Tags')"
                        name="tags"
                        :value="$tags"
                        :tags="$subscriber->emailList->tags()->where('type', \Spatie\Mailcoach\Domain\Audience\Enums\TagType::Default)->pluck('name')->toArray()"
                        :multiple="true"
                        allow-create
                />

                <x-mailcoach::form-buttons>
                    <x-mailcoach::button :label="__mc('Save subscriber')"/>
                </x-mailcoach::form-buttons>
            </form>
        </x-mailcoach::card>

        <x-mailcoach::card class="mt-6">
            <div class="form-field">
                <label class="label">{{ __mc('Created at') }}</label>
                <p class="input py-2 border-transparent hover:border-transparent">{{ $subscriber->created_at->toMailcoachFormat() }}</p>
            </div>

            @if ($subscriber->subscribed_at)
                <div class="form-field">
                    <label class="label">{{ __mc('Subscribed at') }}</label>
                    <p class="input py-2 border-transparent hover:border-transparent">{{ $subscriber->subscribed_at->toMailcoachFormat() }}</p>
                </div>
            @endif

            @if ($subscriber->unsubscribed_at)
                <div class="form-field">
                    <label class="label">{{ __mc('Unsubscribed at') }}</label>
                    <p class="input py-2 border-transparent hover:border-transparent">{{ $subscriber->unsubscribed_at->toMailcoachFormat() }}</p>
                </div>
            @endif
        </x-mailcoach::card>

        <x-mailcoach::fieldset class="mt-6" card :legend="__mc('Usage in Mailcoach API')">
            <div>
                <x-mailcoach::help>
                    {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                    'resourceName' => 'subscriber uuid',
                    'resource' => 'subscriber',
                ]) !!}
                    <p class="mt-4">
                        <x-mailcoach::code-copy class="flex items-center justify-between max-w-md"
                                                :code="$subscriber->uuid"></x-mailcoach::code-copy>
                    </p>
                </x-mailcoach::help>
            </div>
        </x-mailcoach::fieldset>
    @endif

    @if ($tab === 'attributes')
        <x-mailcoach::card>
            <x-mailcoach::info class="markup-code" full>
                {!! __mc('You can add and remove attributes which can then be used in your campaigns or automations using <br><code>&#123;&#123;&nbsp;subscriber.&lt;key&gt;&nbsp;&#125;&#125;</code>') !!}
                <br>
            </x-mailcoach::info>

            <div x-data="{ attributes: @entangle('extra_attributes').live }">
                <template x-for="(attribute, index) in attributes" x-bind:key="index">
                    <div class="my-4 flex items-center w-full gap-x-2">
                        <div class="relative w-full flex items-center">
                            <x-mailcoach::text-field wrapper-class="w-full" x-model="attribute.key" name="key"
                                                     :label="__mc('Key')">
                            </x-mailcoach::text-field>
                            <button type="button" tabindex="-1"
                                    class="absolute right-0 mt-6 mr-2 text-sm ml-1 text-gray-500" @click.prevent="
                                $clipboard('@{{ subscriber.' + attribute.key + ' }}');
                                $el.innerHTML = '<span>Copied!</span>'
                                setTimeout(() => {
                                    $el.innerHTML = '<i class=\'fas fa-copy\'></i>';
                                }, 2000)
                            ">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <x-mailcoach::text-field wrapper-class="w-full" x-model="attribute.value" name="value"
                                                 :label="__mc('Value')"></x-mailcoach::text-field>
                        <button
                                x-on:click.prevent="
                                $dispatch('open-modal', { id: 'confirm' });
                                confirmText = @js(__mc('Are you sure you want to delete this attribute?'));
                                onConfirm = () => attributes.splice(index, 1);
                            "
                                class="mt-auto mb-2 pb-px icon-button text-red-500 hover:text-red-700 cursor-pointer"
                        >
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                </template>
                <div>
                    <x-mailcoach::button-secondary x-on:click.prevent="attributes.push({ key: '', value: '' })">
                        <x-slot:label>
                            <i class="fas fa-plus"></i> {{ __mc('Add attribute') }}
                        </x-slot:label>
                    </x-mailcoach::button-secondary>
                </div>
            </div>

            <x-mailcoach::form-buttons>
                <x-mailcoach::button wire:click.prevent="saveAttributes" :label="__mc('Save subscriber')"/>
            </x-mailcoach::form-buttons>
        </x-mailcoach::card>
    @endif

    @if ($tab === 'sends')
        <livewire:mailcoach::subscriber-sends :subscriber="$subscriber" :email-list="$emailList"/>
    @endif
</div>
