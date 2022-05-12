<form
    x-data="{
        post: @entangle('emailList.allow_form_subscriptions'),
        confirmation: @entangle('emailList.requires_confirmation'),
        confirmationMail: @entangle('confirmation_mail'),
        welcomeMail: @entangle('welcome_mail'),
    }"
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
>
    <x-mailcoach::fieldset :legend="__('mailcoach - Subscriptions')">
        <x-mailcoach::help>
            {!! __('mailcoach - Learn more about <a href=":link" class="link-dimmed" target="_blank">subscription settings and forms</a>.', ['link' => 'https://spatie.be/docs/laravel-mailcoach/v5/using-mailcoach/audience#content-onboarding']) !!}
        </x-mailcoach::help>

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
                <code class="markup-code text-xs ml-8 -mt-1">&lt;form method="POST" action="{{$emailList->incomingFormSubscriptionsUrl()}}"&gt;</code>
            </div>
        </div>

        <div x-show="post" class="pl-8 max-w-xl">
            <x-mailcoach::tags-field
                :label="__('mailcoach - Optionally, allow following subscriber tags')"
                name="allowed_form_subscription_tags"
                :value="$allowed_form_subscription_tags"
                :tags="$emailList->tags->pluck('name')->toArray()"
            />
        </div>
        <x-mailcoach::text-field :label="__('mailcoach - Optionally, allow following subscriber extra Attributes')" :placeholder="__('mailcoach - Attribute(s) comma separated: field1,field2')" name="emailList.allowed_form_extra_attributes" wire:model.lazy="emailList.allowed_form_extra_attributes"/>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Landing Pages')">
        <x-mailcoach::help>
            {!! __('mailcoach - Leave empty to use the defaults. <a class="link-dimmed" target="_blank" href=":link">Example</a>', ['link' => route("mailcoach.landingPages.example")]) !!}
        </x-mailcoach::help>

        <div x-show="confirmation">
            <x-mailcoach::text-field :label="__('mailcoach - Confirm subscription')" placeholder="https://" name="emailList.redirect_after_subscription_pending"
                        wire:model.lazy="emailList.redirect_after_subscription_pending" type="text"/>
        </div>
        <x-mailcoach::text-field :label="__('mailcoach - Someone subscribed')" placeholder="https://" name="emailList.redirect_after_subscribed"
                    wire:model.lazy="emailList.redirect_after_subscribed" type="text"/>
        <x-mailcoach::text-field :label="__('mailcoach - Email was already subscribed')" placeholder="https://"
                    name="emailList.redirect_after_already_subscribed" wire:model.lazy="emailList.redirect_after_already_subscribed"
                    type="text"/>
        <x-mailcoach::text-field :label="__('mailcoach - Someone unsubscribed')" placeholder="https://" name="emailList.redirect_after_unsubscribed"
                    wire:model.lazy="emailList.redirect_after_unsubscribed" type="text"/>
     </x-mailcoach::fieldset>

    <div x-show="confirmation">
        <x-mailcoach::fieldset :legend="__('mailcoach - Confirmation mail')">
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
                    <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="emailList.confirmation_mail_subject"
                                wire:model.lazy="emailList.confirmation_mail_subject" type="text"/>

                    <div class="form-field max-w-full">
                        <label class="label label-required" for="html">{{ __('mailcoach - Body (HTML)') }}</label>
                        <textarea class="input input-html" rows="20" id="html"
                                name="emailList.confirmation_mail_content" wire:model.lazy="emailList.confirmation_mail_content"></textarea>
                        @error('emailList.confirmation_mail_content')
                        <p class="form-error">{{ $message }}</p>
                        @enderror

                        <div class="mt-12 markup-code alert alert-info text-sm">
                            {{ __('mailcoach - You can use following placeholders in the subject and body of the confirmation mail:') }}
                            <ul class="grid mt-2 gap-2">
                                <li><code class="mr-2">::confirmUrl::</code>{{ __('mailcoach - The URL where the subscription can be confirmed') }}</li>
                                <li><code class="mr-2">::subscriber.first_name::</code>{{ __('mailcoach - The first name of the subscriber') }}</li>
                                <li><code class="mr-2">::list.name::</code>{{ __('mailcoach - The name of this list') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <x-mailcoach::help>
                    {{ __('mailcoach - A custom mailable (:mailable) will be used.', ['mailable' => $emailList->welcome_mailable_class]) }}
                </x-mailcoach::help>
            @endif
        </x-mailcoach::fieldset>
    </div>

    <x-mailcoach::fieldset :legend="__('mailcoach - Welcome Mail')">

        @if(empty($emailList->welcome_mailable_class))
            <div class="radio-group">
                <x-mailcoach::radio-field
                    name="welcome_mail"
                    x-model="welcomeMail"
                    option-value="do_not_send_welcome_mail"
                    :label="__('mailcoach - Do not send a welcome mail')"
                />
                <x-mailcoach::radio-field
                    name="welcome_mail"
                    option-value="send_default_welcome_mail"
                    :label="__('mailcoach - Send default welcome mail')"
                    x-model="welcomeMail"
                />
                <x-mailcoach::radio-field
                    name="welcome_mail"
                    option-value="send_custom_welcome_mail"
                    :label="__('mailcoach - Send customized welcome mail')"
                    x-model="welcomeMail"
                />
            </div>

            <div class="form-grid" x-show="welcomeMail !== 'do_not_send_welcome_mail'">
                <x-mailcoach::text-field :label="__('mailcoach - Delay sending welcome mail in minutes')"
                    wire:model.lazy="emailList.welcome_mail_delay_in_minutes"
                    name="emailList.welcome_mail_delay_in_minutes"
                    placeholder="Delay in minutes"/>
            </div>

            <div class="form-grid" x-show="welcomeMail === 'send_custom_welcome_mail'">
                <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="emailList.welcome_mail_subject"
                            wire:model.lazy="emailList.welcome_mail_subject" type="text"/>

                <div class="form-field max-w-full">
                    <label class="label label-required" for="html">{{ __('mailcoach - Body (HTML)') }}</label>
                    <textarea class="input input-html" rows="20" id="html"
                            name="emailList.welcome_mail_content" wire:model="emailList.welcome_mail_content"></textarea>
                    @error('emailList.welcome_mail_content')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="mt-12 markup-code alert alert-info text-sm">
                        {{ __('mailcoach - You can use following placeholders in the subject and body of the welcome mail:') }}
                        <ul class="grid mt-2 gap-2">
                            <li><code class="mr-2">::unsubscribeUrl::</code>{{ __('mailcoach - The URL where users can unsubscribe') }}</li>
                            <li><code class="mr-2">::subscriber.first_name::</code>{{ __('mailcoach - The first name of the subscriber') }}</li>
                            <li><code class="mr-2">::subscriber.email::</code>{{ __('mailcoach - The email of the subscriber') }}</li>
                            <li><code class="mr-2">::list.name::</code>{{ __('mailcoach - The name of this list') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <x-mailcoach::help>
                {{ __('mailcoach - A custom mailable (:mailable) will be used.', ['mailable' => $emailList->welcome_mailable_class]) }}
            </x-mailcoach::help>
        @endif
    </x-mailcoach::fieldset>

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </div>
</form>

