<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailTestJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;

class AutomationMail extends Sendable
{
    public $table = 'mailcoach_automation_mails';

    public function links(): HasMany
    {
        return $this->hasMany(AutomationMailLink::class, 'automation_mail_id');
    }

    public function opens(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                AutomationMailOpen::class,
                Send::class,
                'automation_mail_id'
            )
            ->orderBy('created_at');
    }

    public function clicks(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                AutomationMailClick::class,
                Send::class,
                'automation_mail_id'
            )
            ->orderBy('created_at');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(Send::class, 'automation_mail_id');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(AutomationMailUnsubscribe::class, 'automation_mail_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, Send::class, 'automation_mail_id')
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, Send::class, 'automation_mail_id')
            ->where('type', SendFeedbackType::COMPLAINT);
    }

    public function isReady(): bool
    {
        if (! $this->html) {
            return false;
        }

        if (! $this->hasValidHtml()) {
            return false;
        }

        if (! $this->subject) {
            return false;
        }

        return true;
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->ensureUpdatable();

        if (! is_a($mailableClass, MailcoachMail::class, true)) {
            throw CouldNotSendAutomationMail::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass, 'mailable_arguments' => $mailableArguments]);

        return $this;
    }

    public function contentFromMailable(): string
    {
        return $this
            ->getMailable()
            ->setSendable($this)
            ->render();
    }

    public function pullSubjectFromMailable(): void
    {
        if (! $this->hasCustomMailable()) {
            return;
        }

        $mailable = $this->getMailable()->setSendable($this);
        $mailable->build();

        if (! empty($mailable->subject)) {
            $this->subject($mailable->subject);
        }
    }

    public function send(Subscriber $subscriber): self
    {
        $this->ensureSendable();

        if (empty($this->from_email)) {
            $this->from_email = $subscriber->emailList->default_from_email ?? config('mail.from.address');
            $this->save();
        }

        if (empty($this->from_name)) {
            $this->from_name = $subscriber->emailList->default_from_name ?? config('mail.from.name');
            $this->save();
        }

        if (empty($this->reply_to_email)) {
            $this->reply_to_email = $subscriber->emailList->default_reply_to_email;
            $this->save();
        }

        if (empty($this->reply_to_name)) {
            $this->reply_to_name = $subscriber->emailList->default_reply_to_name;
            $this->save();
        }

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        dispatch(new SendAutomationMailToSubscriberJob($this, $subscriber));

        return $this;
    }

    public function wasAlreadySentToSubscriber(Subscriber $subscriber): bool
    {
        return $this
            ->sends()
            ->whereNotNull('sent_at')
            ->where('subscriber_id', $subscriber->id)
            ->exists();
    }

    public function sendTestMail(string | array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch(new SendAutomationMailTestJob($this, $email));
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.automations.webview', $this->uuid));
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    public function dispatchCalculateStatistics()
    {
        $lock = new CalculateStatisticsLock($this);

        if (! $lock->get()) {
            return;
        }

        dispatch(new CalculateStatisticsJob($this));
    }

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === MailcoachMail::class) {
            return false;
        }

        return ! is_null($this->mailable_class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getAutomationMailClass()::where($field, $value)->firstOrFail();
    }

    public function fromEmail(): string
    {
        return $this->from_email ?? config('mail.from.address');
    }

    public function fromName(): ?string
    {
        return $this->from_name;
    }
}
