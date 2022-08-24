<form
        x-data="{
        post: @entangle('emailList.allow_form_subscriptions'),
        confirmation: @entangle('emailList.requires_confirmation'),
        confirmationMail: @entangle('confirmation_mail'),
    }"
        class="card-grid"
        method="POST"
        wire:submit.prevent="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::fieldset card :legend="__('mailcoach - Subscriptions')">
        <x-mailcoach::info>
            {!! __('mailcoach - Learn more about <a href=":link" target="_blank">subscription settings and forms</a>.', ['link' => 'https://mailcoach.app/docs/v5/mailcoach/using-mailcoach/audience#content-onboarding']) !!}
        </x-mailcoach::info>

        <div class="form-field max-w-full">
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field
                        :label="__('mailcoach - Require confirmation')"
                        name="emailList.requires_confirmation"
                        x-model="confirmation"
                />

                <x-mailcoach::checkbox-field
                        :label="__('mailcoach - Allow POST from an external form')"
                        name="emailList.allow_form_subscriptions"
                        x-model="post"
                />

                <div x-show="post" class="pl-8">
                    <x-mailcoach::code-copy button-class="w-full text-right -mb-6" button-position="top" :code="$emailList->getSubscriptionFormHtml()"/>
                </div>
            </div>
        </div>

        <div x-show="post" class="pl-8 max-w-xl">
            <x-mailcoach::tags-field
                :label="__('mailcoach - Optionally, allow following subscriber tags')"
                name="allowed_form_subscription_tags"
                :value="$allowed_form_subscription_tags"
                :tags="$emailList->tags->pluck('name')->unique()->toArray()"
            />
        </div>
        <div x-show="post" class="pl-8 max-w-xl">
            <x-mailcoach::text-field
                :label="__('mailcoach - Optionally, allow following subscriber extra Attributes')"
                :placeholder="__('mailcoach - Attribute(s) comma separated: field1,field2')"
                name="emailList.allowed_form_extra_attributes"
                wire:model.lazy="emailList.allowed_form_extra_attributes"
            />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__('mailcoach - Landing Pages')">
        <x-mailcoach::info>
            {!! __('mailcoach - Leave empty to use the defaults. <a target="_blank" href=":link">Example</a>', ['link' => route("mailcoach.landingPages.example")]) !!}
        </x-mailcoach::info>

        <div x-show="confirmation">
            <x-mailcoach::text-field :label="__('mailcoach - Confirm subscription')" placeholder="https://"
                                     name="emailList.redirect_after_subscription_pending"
                                     wire:model.lazy="emailList.redirect_after_subscription_pending" type="text"/>
        </div>
        <x-mailcoach::text-field :label="__('mailcoach - Someone subscribed')" placeholder="https://"
                                 name="emailList.redirect_after_subscribed"
                                 wire:model.lazy="emailList.redirect_after_subscribed" type="text"/>
        <x-mailcoach::text-field :label="__('mailcoach - Email was already subscribed')" placeholder="https://"
                                 name="emailList.redirect_after_already_subscribed"
                                 wire:model.lazy="emailList.redirect_after_already_subscribed"
                                 type="text"/>
        <x-mailcoach::text-field :label="__('mailcoach - Someone unsubscribed')" placeholder="https://"
                                 name="emailList.redirect_after_unsubscribed"
                                 wire:model.lazy="emailList.redirect_after_unsubscribed" type="text"/>
    </x-mailcoach::fieldset>

    <div x-show="confirmation">
        <x-mailcoach::fieldset card :legend="__('mailcoach - Confirmation mail')">
            @if(empty($emailList->confirmation_mailable_class))
                <div class="radio-group">
                    <x-mailcoach::radio-field
                            name="confirmation_mail"
                            option-value="send_default_confirmation_mail"
                            :label="__('mailcoach - Send default confirmation mail')"
                            x-model="confirmationMail"
                    />
                    <x-mailcoach::radio-field
                            name="confirmation_mail"
                            option-value="send_custom_confirmation_mail"
                            :label="__('mailcoach - Send customized confirmation mail')"
                            x-model="confirmationMail"
                    />
                </div>

                <div class="form-grid" x-show="confirmationMail === 'send_custom_confirmation_mail'">
                    @if (count($transactionalMailTemplates))
                        <div class="flex items-center gap-x-2 max-w-sm">
                            <div class="w-full">
                                <x-mailcoach::select-field
                                    wire:model="emailList.confirmation_mail_id"
                                    name="emailList.confirmation_mail_id"
                                    :options="$transactionalMailTemplates"
                                    :placeholder="__('mailcoach - Select a transactional mail template')"
                                />
                            </div>
                            @if ($emailList->confirmationMail)
                                <a href="{{ route('mailcoach.transactionalMails.templates.edit', $emailList->confirmationMail) }}" class="link">{{ __('mailcoach - Edit') }}</a>
                            @endif
                        </div>
                    @else
                        <x-mailcoach::info>
                            {!! __('mailcoach - You need to create a transactional mail template first. <a href=":createLink" class="link">Create one here</a>', [
                                'createLink' => route('mailcoach.transactionalMails.templates'),
                            ]) !!}
                        </x-mailcoach::info>
                    @endif

                    <x-mailcoach::help class="markup-code lg:max-w-3xl">
                        {{ __('mailcoach - You can use the following placeholders in the subject and body of the confirmation mail:') }}
                        <dl class="mt-4 markup-dl">
                            <dt><code>::confirmUrl::</code></dt>
                            <dd>{{ __('mailcoach - The URL where the subscription can be confirmed') }}</dd>
                            <dt><code>::subscriber.first_name::</code></dt>
                            <dd>{{ __('mailcoach - The first name of the subscriber') }}</dd>
                            <dt><code>::list.name::</code></dt>
                            <dd>{{ __('mailcoach - The name of this list') }}</dd>
                        </dl>
                    </x-mailcoach::help>
                </div>
            @else
                <x-mailcoach::info>
                    {{ __('mailcoach - A custom mailable (:mailable) will be used.', ['mailable' => $emailList->confirmation_mailable_class]) }}
                </x-mailcoach::info>
            @endif
        </x-mailcoach::fieldset>
    </div>

    <x-mailcoach::fieldset card :legend="__('mailcoach - Welcome Mail')">
        <x-mailcoach::help>
            {!! __('mailcoach - Check out the <a href=":docsUrl" class="link">documentation</a> to learn how to set up a welcome automation.', [
                'docsUrl' => '@todo: Link to docs'
            ]) !!}
        </x-mailcoach::help>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </x-mailcoach::card>
</form>
