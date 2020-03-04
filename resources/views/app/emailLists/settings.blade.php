@extends('mailcoach::app.emailLists.layouts.edit', [
    'emailList' => $emailList,
    'titlePrefix' => 'Settings'
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.emailLists.subscribers', $emailList) }}">
            <span class="breadcrumb">{{ $emailList->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">Settings</span></li>
@endsection

@section('emailList')
    <form class="form-grid" action="{{ route('mailcoach.emailLists.settings', $emailList) }}" method="POST">
        @csrf
        @method('PUT')

        <c-text-field label="Name" name="name" :value="$emailList->name"/>

        <c-text-field label="From email" name="default_from_email" :value="$emailList->default_from_email"
                      type="email"/>

        <c-text-field label="From name" name="default_from_name" :value="$emailList->default_from_name"/>

        <div class="form-row max-w-full">
            <label class="label">Publish feed</label>
            <c-checkbox-field label="Make feed publicly available" name="campaigns_feed_enabled"
                              :checked="$emailList->campaigns_feed_enabled"/>
            <a class="text-sm link ml-8 -mt-2" href="{{$emailList->feedUrl()}}">{{$emailList->feedUrl()}}</a>
        </div>

        <hr class="border-t-2 border-gray-200 my-8">

        <h2 class="markup-h2">Reports</h2>

        <div class="form-row">
            <label class="label">Send a…</label>
            <div class="checkbox-group">
                <c-checkbox-field label="Confirmation when a campaign gets sent to this list" name="report_campaign_sent" :checked="$emailList->report_campaign_sent" />
                <c-checkbox-field label="Summary of opens, clicks & bounces a day after a campaign to this list has been sent" name="report_campaign_summary" :checked="$emailList->report_campaign_summary" />
                <c-checkbox-field label="Weekly summary on the subscriber growth of this list" name="report_email_list_summary" :checked="$emailList->report_email_list_summary" />
            </div>
        </div>

        <c-text-field placeholder="Email(s) comma separated" label="To…" name="report_recipients" :value="$emailList->report_recipients"/>

        <hr class="border-t-2 border-gray-200 my-8">

        <h2 class="markup-h2">Subscriptions</h2>

        <div class="form-row max-w-full">
            <div class="checkbox-group">
                <c-checkbox-field dataConditional="confirmation" label="Require confirmation" name="requires_confirmation"
                                  :checked="$emailList->requires_confirmation"/>

                <c-checkbox-field dataConditional="post" label="Allow POST from an external form"
                                  name="allow_form_subscriptions"
                                  :checked="$emailList->allow_form_subscriptions"/>
                <code class="markup-code text-xs ml-8 -mt-1">&lt;form
                    action="{{$emailList->incomingFormSubscriptionsUrl()}}"&gt;</code>

            </div>
        </div>

        <div data-conditional-post="true" class="pl-8 max-w-lg">
            <c-tags-field
                label="Optionally, allow following subscriber tags"
                name="allowed_form_subscription_tags"
                :value="$emailList->allowedFormSubscriptionTags()->pluck('name')->toArray()"
                :tags="$emailList->tags()->pluck('name')->toArray()"
            />

            <p class="form-note">Learn more on <a href="https://mailcoach.app/docs/app/lists/settings#subscriptions" class="link-dimmed" target="_blank">these form
                    settings</a>.</p>
        </div>

        <hr class="border-t-2 border-gray-200 my-8">

        <h2 class="markup-h2">Landing pages</h2>

        <c-help>
        Leave empty to use the defaults. <a class="link-dimmed" target="_blank" href="{{ route("mailcoach.landingPages.example")}}">Example</a>
        </c-help>

        <div data-conditional-confirmation="true">
            <c-text-field label="Confirm subscription" placeholder="https://" name="redirect_after_subscription_pending"
                          :value="$emailList->redirect_after_subscription_pending" type="text"/>
        </div>
        <c-text-field label="Someone subscribed" placeholder="https://" name="redirect_after_subscribed"
                      :value="$emailList->redirect_after_subscribed" type="text"/>
        <c-text-field label="Email was already subscribed" placeholder="https://"
                      name="redirect_after_already_subscribed" :value="$emailList->redirect_after_already_subscribed"
                      type="text"/>
        <c-text-field label="Someone unsubscribed" placeholder="https://" name="redirect_after_unsubscribed"
                      :value="$emailList->redirect_after_unsubscribed" type="text"/>

        <hr class="border-t-2 border-gray-200 my-8">

        <h2 class="markup-h2">Welcome mail</h2>

        @if(empty($emailList->welcome_mailable_class))
            <div class="radio-group">
                <c-radio-field
                    name="welcome_mail"
                    option-value="do_not_send_welcome_mail"
                    :value="! $emailList->send_welcome_mail"
                    label="Do not send a welcome mail"
                    data-conditional="welcome-mail"
                />
                <c-radio-field
                    name="welcome_mail"
                    option-value="send_default_welcome_mail"
                    :value="($emailList->send_welcome_mail) && (! $emailList->hasCustomizedWelcomeMailFields())"
                    label="Send default welcome mail"
                    data-conditional="welcome-mail"
                />
                <c-radio-field
                    name="welcome_mail"
                    option-value="send_custom_welcome_mail"
                    :value="$emailList->send_welcome_mail && $emailList->hasCustomizedWelcomeMailFields()"
                    label="Send customized welcome mail"
                    data-conditional="welcome-mail"
                />
            </div>

            <div class="form-grid" data-conditional-welcome-mail="send_custom_welcome_mail">
                <c-text-field label="Subject" name="welcome_mail_subject"
                              :value="$emailList->welcome_mail_subject" type="text"/>

                <div class="form-row max-w-full">
                    <label class="label label-required" for="html">Body (HTML)</label>
                    <textarea class="input input-html" rows="20" id="html"
                              name="welcome_mail_content">{{ old('welcome_mail_content', $emailList->welcome_mail_content) }}</textarea>
                    @error('welcome_mail_content')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="mt-12 markup-code alert alert-info text-sm">
                        You can use following placeholders in the subject and body of the welcome mail:
                        <ul class="grid mt-2 gap-2">
                            <li><code class="mr-2">::unsubscribeUrl::</code>The url where users can unsubscribe</li>
                            <li><code class="mr-2">::subscriber.first_name::</code>The first name of the subscriber</li>
                            <li><code class="mr-2">::subscriber.email::</code>The email of the subscriber</li>
                            <li><code class="mr-2">::list.name::</code>The name of this list</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <c-help>
                A custom mailable ({{ $emailList->welcome_mailable_class }}) will be used.
            </c-help>
        @endif

        <div class="form-grid" data-conditional-optin="true">
            <hr class="border-t-2 border-gray-200 my-8">

            <h2 class="markup-h2">Confirmation mail</h2>

            @if(empty($emailList->confirmation_mailable_class))
                <div class="radio-group">
                    <c-radio-field
                        name="confirmation_mail"
                        option-value="send_default_confirmation_mail"
                        :value="! $emailList->hasCustomizedConfirmationMailFields()"
                        label="Send default confirmation mail"
                        data-conditional="confirmation-mail"
                    />
                    <c-radio-field
                        name="confirmation_mail"
                        option-value="send_custom_confirmation_mail"
                        :value="$emailList->hasCustomizedConfirmationMailFields()"
                        label="Send customized confirmation mail"
                        data-conditional="confirmation-mail"
                    />
                </div>

                <div class="form-grid" data-conditional-confirmation-mail="send_custom_confirmation_mail">
                    <c-text-field label="Subject" name="confirmation_mail_subject"
                                  :value="$emailList->confirmation_mail_subject" type="text"/>

                    <div class="form-row max-w-full">
                        <label class="label label-required" for="html">Body (HTML)</label>
                        <textarea class="input input-html" rows="20" id="html"
                                  name="confirmation_mail_content">{{ old('confirmation_mail_content', $emailList->confirmation_mail_content) }}</textarea>
                        @error('confirmation_mail_content')
                        <p class="form-error">{{ $message }}</p>
                        @enderror

                        <div class="mt-12 markup-code alert alert-info text-sm">
                            You can use following placeholders in the subject and body of the confirmation mail:
                            <ul class="grid mt-2 gap-2">
                                <li><code class="mr-2">::confirmUrl::</code>The url where the suscription can be confirmed</li>
                                <li><code class="mr-2">::subscriber.first_name::</code>The first name of the subscriber</li>
                                <li><code class="mr-2">::list.name::</code>The name of this list</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <c-help>
                    A custom mailable ({{ $emailList->confirmation_mailable_class }}) will be used.
                </c-help>
            @endif

        </div>

        <div class="form-buttons">
            <button type="submit" class="button">
                <c-icon-label icon="fa-cog" text="Save list settings"/>
            </button>
        </div>
    </form>
@endsection
