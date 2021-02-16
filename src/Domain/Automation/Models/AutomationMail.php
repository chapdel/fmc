<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use DOMDocument;
use DOMElement;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailTestJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotUpdateCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class AutomationMail extends Sendable implements Feedable, HasHtmlContent
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

    public function isEditable(): bool
    {
        return true;
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->ensureUpdatable();

        if (! is_a($mailableClass, MailcoachMail::class, true)) {
            throw CouldNotSendCampaign::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass, 'mailable_arguments' => $mailableArguments]);

        return $this;
    }

    public function contentFromMailable(): string
    {
        return $this
            ->getMailable()
            ->setCampaign($this)
            ->render();
    }

    public function pullSubjectFromMailable(): void
    {
        if (! $this->hasCustomMailable()) {
            return;
        }

        $mailable = $this->getMailable()->setCampaign($this);
        $mailable->build();

        if (! empty($mailable->subject)) {
            $this->subject($mailable->subject);
        }
    }

    public function send(Subscriber $subscriber): self
    {
        $this->ensureSendable();

        if (empty($this->from_email)) {
            $this->from_email = config('mail.from.address');
            $this->save();
        }

        if (empty($this->from_name)) {
            $this->from_name = config('mail.from.name');
            $this->save();
        }

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        dispatch(new SendAutomationMailToSubscriberJob($this, $subscriber));

        return $this;
    }

    public function sendTo(EmailList $emailList): self
    {
        return $this->to($emailList)->send();
    }

    protected function ensureSendable()
    {
        if ($this->hasCustomMailable()) {
            return;
        }
    }

    public function markAsSent(int $numberOfSubscribers): self
    {
        $this->update([
            'status' => CampaignStatus::SENT,
            'sent_at' => now(),
            'statistics_calculated_at' => now(),
            'sent_to_number_of_subscribers' => $numberOfSubscribers,
        ]);

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return $this->isSent();
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
        return (string)url(route('mailcoach.automations.webview', $this->uuid));
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return app($mailableClass, $mailableArguments);
    }

    /** TODO: make automation specific */
    public function dispatchCalculateStatistics()
    {
        $lock = new CalculateStatisticsLock($this);

        if (! $lock->get()) {
            return;
        }

        dispatch(new CalculateStatisticsJob($this));
    }

    public function toFeedItem(): FeedItem
    {
        return (new FeedItem())
            ->author('Mailcoach')
            ->link($this->webviewUrl())
            ->title($this->subject)
            ->id($this->uuid)
            ->summary('')
            ->updated($this->sent_at);
    }

    public function emailListSubscriberCount(): int
    {
        if (! $this->emailList) {
            return 0;
        }

        return $this->emailList->subscribers()->count();
    }

    public function sendsCount(): int
    {
        return $this->sends()->whereNotNull('sent_at')->count();
    }

    public function wasSentToAllSubscribers(): bool
    {
        if (! $this->isSent()) {
            return false;
        }

        return $this->sends()->pending()->count() === 0;
    }

    protected function ensureUpdatable(): void
    {
        if ($this->isSending()) {
            throw CouldNotUpdateCampaign::beingSent($this);
        }

        if ($this->isSent()) {
            throw CouldNotSendCampaign::alreadySent($this);
        }

        if ($this->isCancelled()) {
            throw CouldNotSendCampaign::cancelled($this);
        }
    }

    protected function markAsSending(): self
    {
        $this->update([
            'status' => CampaignStatus::SENDING,
        ]);

        return $this;
    }

    public function hasTroublesSendingOutMails(): bool
    {
        if ($this->status !== CampaignStatus::SENDING) {
            return false;
        }

        if (! $this->last_modified_at) {
            return false;
        }

        if ($this->last_modified_at->diffInHours() < 1) {
            return false;
        }

        $latestSend = $this
            ->sends()
            ->whereNotNull('sent_at')
            ->orderByDesc('sent_at')
            ->first();

        if ($latestSend->sent_at->diffInHours() < 1) {
            return false;
        }

        return true;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatus::DRAFT;
    }

    public function isSending(): bool
    {
        return $this->status == CampaignStatus::SENDING;
    }

    public function isSent(): bool
    {
        return $this->status == CampaignStatus::SENT;
    }

    public function isSendingOrSent(): bool
    {
        return $this->isSending() || $this->isSent();
    }

    public function isCancelled(): bool
    {
        return $this->status == CampaignStatus::CANCELLED;
    }

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === MailcoachMail::class) {
            return false;
        }

        return ! is_null($this->mailable_class);
    }

    public function htmlWithInlinedCss(): string
    {
        $html = $this->getHtml();

        if ($this->hasCustomMailable()) {
            $html = $this->contentFromMailable();
        }

        return (new CssToInlineStyles())->convert($html ?? '');
    }

    public function htmlLinks(): Collection
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $value = preg_replace('/&(?!amp;)/', '&amp;', $this->getHtml());

        if ($value === '') {
            return collect();
        }

        $dom->loadHTML($value, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);

        return collect($dom->getElementsByTagName('a'))
            ->map(function (DOMElement $link) {
                return $link->getAttribute('href');
            })->reject(function (string $url) {
                return str_contains($url, '::');
            });
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    public function sizeInKb(): int
    {
        return ceil(mb_strlen($this->getHtml(), '8bit') / 1000);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getCampaignClass()::where($field, $value)->firstOrFail();
    }

    public function getBatchName(): string
    {
        return Str::slug("{$this->name} ({$this->id})");
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
