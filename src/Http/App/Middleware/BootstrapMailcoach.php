<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Domain\Shared\Events\ServingMailcoach;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class BootstrapMailcoach
{
    use UsesMailcoachModels;

    public function handle($request, $next)
    {
        $this->bootstrapRouteModels();

        if (config('mailcoach.guard')) {
            config()->set('auth.defaults.guard', config('mailcoach.guard'));
        }

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('mailcoach.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Notification::configureUsing(function (Notification $notification): void {
            $notification->view('mailcoach::app.layouts.partials.notification');
        });

        ServingMailcoach::dispatch();

        return $next($request);
    }

    protected function bootstrapRouteModels(): void
    {
        // Audience
        Route::model('emailList', self::getEmailListClass());
        Route::model('subscriber', self::getSubscriberClass());
        Route::model('subscriberImport', self::getSubscriberImportClass());
        Route::model('tag', self::getTagClass());
        Route::model('tagSegment', self::getTagSegmentClass());
        Route::model('segment', self::getTagSegmentClass());
        Route::model('action', self::getAutomationActionClass());

        // Automation
        Route::model('action', self::getAutomationActionClass());
        Route::model('actionSubscriber', self::getActionSubscriberClass());
        Route::model('automation', self::getAutomationClass());
        Route::model('automationMail', self::getAutomationMailClass());
        Route::model('trigger', self::getAutomationTriggerClass());

        // Campaign
        Route::model('campaign', self::getCampaignClass());

        // Content
        Route::model('click', self::getClickClass());
        Route::model('link', self::getLinkClass());
        Route::model('open', self::getOpenClass());
        Route::model('unsubscribe', self::getUnsubscribeClass());
        Route::model('template', self::getTemplateClass());

        // Settings
        Route::model('mailer', self::getMailerClass());
        Route::model('setting', self::getSettingClass());
        Route::model('webhookConfiguration', self::getWebhookConfigurationClass());

        // Shared
        Route::model('send', self::getSendClass());
        Route::model('sendFeedbackItem', self::getSendFeedbackItemClass());
        Route::model('upload', self::getUploadClass());
        Route::model('webhook', self::getWebhookConfigurationClass());
        Route::model('webhookLog', self::getWebhookLogClass());

        // Transactional
        Route::model('transactionalMail', self::getTransactionalMailLogItemClass());
        Route::model('transactionalMailTemplate', self::getTransactionalMailClass());
    }
}
