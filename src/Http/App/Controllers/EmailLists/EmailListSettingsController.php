<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Requests\UpdateEmailListSettingsRequest;
use Spatie\Mailcoach\Models\EmailList;

class EmailListSettingsController
{
    public function edit(EmailList $emailList)
    {
        return view('mailcoach::app.emailLists.settings', [
            'emailList' => $emailList,
        ]);
    }

    public function update(EmailList $emailList, UpdateEmailListSettingsRequest $request)
    {
        $emailList->update([
            'name' => $request->name,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'campaign_mailer' => $request->campaign_mailer,
            'transactional_mailer' => $request->transactional_mailer,
            'campaigns_feed_enabled' => $request->campaigns_feed_enabled ?? false,
            'report_recipients' => $request->report_recipients,
            'report_campaign_sent' => $request->report_campaign_sent ?? false,
            'report_campaign_summary' => $request->report_campaign_summary ?? false,
            'report_email_list_summary' => $request->report_email_list_summary ?? false,
            'allow_form_subscriptions' => $request->allow_form_subscriptions ?? false,
            'requires_confirmation' => $request->requires_confirmation ?? false,
            'redirect_after_subscribed' => $request->redirect_after_subscribed,
            'redirect_after_already_subscribed' => $request->redirect_after_already_subscribed,
            'redirect_after_subscription_pending' => $request->redirect_after_subscription_pending,
            'redirect_after_unsubscribed' => $request->redirect_after_unsubscribed,
            'send_welcome_mail' => $request->sendWelcomeMail(),
            'welcome_mail_subject' => $request->welcome_mail === UpdateEmailListSettingsRequest::WELCOME_MAIL_CUSTOM_CONTENT
                ? $request->welcome_mail_subject
                : '',
            'welcome_mail_content' => $request->welcome_mail === UpdateEmailListSettingsRequest::WELCOME_MAIL_CUSTOM_CONTENT
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
