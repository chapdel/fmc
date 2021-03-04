<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\UpdateEmailListGeneralSettingsRequest;

class EmailListMailersController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.settings.mailers', [
            'emailList' => $emailList,
        ]);
    }

    public function update(EmailList $emailList, UpdateEmailListGeneralSettingsRequest $request)
    {
        $this->authorize('update', $emailList);

        $emailList->update([
            'name' => $request->name,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'default_reply_to_email' => $request->default_reply_to_email,
            'default_reply_to_name' => $request->default_reply_to_name,
            'campaign_mailer' => $request->campaign_mailer,
            'transactional_mailer' => $request->transactional_mailer,
            'campaigns_feed_enabled' => $request->campaigns_feed_enabled ?? false,
            'report_recipients' => $request->report_recipients,
            'report_campaign_sent' => $request->report_campaign_sent ?? false,
            'report_campaign_summary' => $request->report_campaign_summary ?? false,
            'report_email_list_summary' => $request->report_email_list_summary ?? false,
            'allow_form_subscriptions' => $request->allow_form_subscriptions ?? false,
            'allowed_form_extra_attributes' => $request->allowed_form_extra_attributes,
            'requires_confirmation' => $request->requires_confirmation ?? false,
            'redirect_after_subscribed' => $request->redirect_after_subscribed,
            'redirect_after_already_subscribed' => $request->redirect_after_already_subscribed,
            'redirect_after_subscription_pending' => $request->redirect_after_subscription_pending,
            'redirect_after_unsubscribed' => $request->redirect_after_unsubscribed,
            'send_welcome_mail' => $request->sendWelcomeMail(),
            'welcome_mail_subject' => $request->welcome_mail === UpdateEmailListGeneralSettingsRequest::WELCOME_MAIL_CUSTOM_CONTENT
                ? $request->welcome_mail_subject
                : '',
            'welcome_mail_content' => $request->welcome_mail === UpdateEmailListGeneralSettingsRequest::WELCOME_MAIL_CUSTOM_CONTENT
                ? $request->welcome_mail_content
                : '',
            'welcome_mail_delay_in_minutes' => $request->welcome_mail_delay_in_minutes ?? 0,
            'confirmation_mail_subject' => $request->sendDefaultConfirmationMail() ? null : $request->confirmation_mail_subject,
            'confirmation_mail_content' => $request->sendDefaultConfirmationMail() ? null : $request->confirmation_mail_content,
        ]);

        $emailList->allowedFormSubscriptionTags()->sync($request->allowedFormSubscriptionTags());

        flash()->success(__('List :emailList was updated', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.settings', $emailList->id);
    }
}
