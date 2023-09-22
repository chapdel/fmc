<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Actions\ValidateCampaignRequirementsAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotUpdateCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\CanBeScheduled;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Content\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Actions\InitializeMjmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Mailcoach;
use Throwable;

/**
 * @method static Builder|static query()
 */
class Campaign extends Sendable implements Feedable
{
    use CanBeScheduled;
    use SendsToSegment;

    public $table = 'mailcoach_campaigns';

    public $casts = [
        'sent_to_number_of_subscribers' => 'integer',
        'scheduled_at' => 'datetime',
        'campaigns_feed_enabled' => 'boolean',
        'add_subscriber_tags' => 'boolean',
        'add_subscriber_link_tags' => 'boolean',
        'all_sends_created_at' => 'datetime',
        'all_sends_dispatched_at' => 'datetime',
        'summary_mail_sent_at' => 'datetime',
        'status' => CampaignStatus::class,
        'show_publicly' => 'boolean',
        'disable_webview' => 'boolean',
    ];

    public static function booted()
    {
        static::creating(function (Campaign $campaign) {
            if (! $campaign->status) {
                $campaign->status = CampaignStatus::Draft;
            }
        });

        static::created(function (Campaign $campaign) {
            if (! $campaign->contentItem) {
                $contentItem = $campaign->contentItem()->firstOrCreate();
                $campaign->setRelation('contentItem', $contentItem);
            }
        });

        static::deleting(function (Campaign $campaign) {
            $campaign->contentItem->delete();
        });
    }

    public static function scopeSentBetween(Builder $query, CarbonInterface $periodStart, CarbonInterface $periodEnd): void
    {
        $query
            ->whereNotNull('sent_at')
            ->whereNotNull('all_sends_dispatched_at')
            ->whereNotNull('all_sends_created_at')
            ->where('sent_at', '>=', $periodStart)
            ->where('sent_at', '<', $periodEnd);
    }

    public function scopeDraft(Builder $query): void
    {
        $query
            ->where('status', CampaignStatus::Draft)
            ->whereNull('scheduled_at')
            ->orderBy('created_at');
    }

    public function scopeSendingOrSent(Builder $query): void
    {
        $query->whereIn('status', [CampaignStatus::Sending, CampaignStatus::Sent]);
    }

    public function scopeShowPublicly(Builder $query): void
    {
        $query
            ->sendingOrSent()
            ->where('show_publicly', true);
    }

    public function scopeSending(Builder $query): void
    {
        $query->where('status', CampaignStatus::Sending);
    }

    public function scopeNeedsSummaryToBeReported(Builder $query)
    {
        $query
            ->whereHas(
                'emailList',
                fn (Builder $query) => $query->where('report_campaign_summary', true)
            )
            ->whereNull('summary_mail_sent_at');
    }

    public function scopeSent(Builder $query): void
    {
        $query->where('status', CampaignStatus::Sent);
    }

    public function scopeSentDaysAgo(Builder $query, int $daysAgo)
    {
        $query
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now()->subDays($daysAgo)->toDateTimeString());
    }

    /**
     * Returns a tuple with open & click tracking values
     */
    public function tracking(): array
    {
        $mailer = $this->getMailer();

        if (! $this->emailList) {
            return [null, null];
        }

        return [
            $mailer?->get('open_tracking_enabled', false),
            $mailer?->get('click_tracking_enabled', false),
        ];
    }

    public function getMailerKey(): ?string
    {
        return $this->emailList->campaign_mailer
            ?? Mailcoach::defaultCampaignMailer();
    }

    public function getMailer(): ?Mailer
    {
        $mailerClass = config('mailcoach.models.mailer');

        if (! class_exists($mailerClass)) {
            return null;
        }

        if (! $mailerKey = $this->getMailerKey()) {
            return null;
        }

        return $mailerClass::all()->first(fn ($mailerModel) => $mailerKey === $mailerModel->configName());
    }

    public function isReady(): bool
    {
        if (! $this->contentItem->html) {
            return false;
        }

        if (! $this->contentItem->subject) {
            return false;
        }

        if (! optional($this->emailList)->default_from_email) {
            return false;
        }

        if (! $this->emailListSubscriberCount()) {
            return false;
        }

        if (! $this->getMailerKey()) {
            return false;
        }

        return count($this->validateRequirements()) === 0;
    }

    public function validateRequirements(): array
    {
        /** @var ValidateCampaignRequirementsAction $action */
        $action = Mailcoach::getCampaignActionClass('validate_campaign_requirements', ValidateCampaignRequirementsAction::class);

        return $action->execute($this);
    }

    public function isPending(): bool
    {
        return ! in_array($this->status, [
            CampaignStatus::Sending,
            CampaignStatus::Sent,
        ]);
    }

    public function isScheduled(): bool
    {
        return $this->isDraft() && $this->scheduled_at;
    }

    public function isEditable(): bool
    {
        if ($this->isSending()) {
            return false;
        }

        if ($this->isSent()) {
            return false;
        }

        if ($this->isCancelled()) {
            return false;
        }

        return true;
    }

    public function to(EmailList $emailList): self
    {
        $this->ensureUpdatable();

        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function send(): self
    {
        $this->ensureSendable();

        if (empty($this->contentItem->from_email)) {
            $this->contentItem->from_email = $this->emailList->default_from_email;
        }

        if (empty($this->contentItem->from_name)) {
            $this->contentItem->from_name = $this->emailList->default_from_name;
        }

        if (empty($this->contentItem->reply_to_email)) {
            $this->contentItem->reply_to_email = $this->emailList->default_reply_to_email;
        }

        if (empty($this->contentItem->reply_to_name)) {
            $this->contentItem->reply_to_name = $this->emailList->default_reply_to_name;
        }

        if ($this->contentItem->hasCustomMailable()) {
            $this->contentItem->pullSubjectFromMailable();

            $this->contentItem->content($this->contentItem->contentFromMailable());
        }

        $this->contentItem->save();

        $this->segment_description = $this->getSegment()->description();
        $this->save();

        $this->markAsSending();

        return $this;
    }

    public function cancel(): self
    {
        $this->update([
            'status' => CampaignStatus::Cancelled,
            'sent_at' => now(),
        ]);

        return $this;
    }

    public function sendTo(EmailList $emailList): self
    {
        return $this->to($emailList)->send();
    }

    protected function ensureSendable()
    {
        if ($this->isSending()) {
            throw CouldNotSendCampaign::beingSent($this);
        }

        if ($this->isSent()) {
            throw CouldNotSendCampaign::alreadySent($this);
        }

        if ($this->isCancelled()) {
            throw CouldNotSendCampaign::cancelled($this);
        }

        if (is_null($this->emailList)) {
            throw CouldNotSendCampaign::noListSet($this);
        }

        if ($this->hasCustomMailable()) {
            return;
        }

        if (empty($this->from_email) && empty($this->emailList->default_from_email)) {
            throw CouldNotSendCampaign::noFromEmailSet($this);
        }

        if (empty($this->contentItem->subject)) {
            throw CouldNotSendCampaign::noSubjectSet($this);
        }

        if (empty($this->contentItem->html)) {
            throw CouldNotSendCampaign::noContent($this);
        }

        if (count($this->validateRequirements()) > 0) {
            throw CouldNotSendCampaign::requirementsNotMet($this);
        }

        if (containsMjml($this->contentItem->html) && ! Mailcoach::getSharedActionClass('initialize_mjml', InitializeMjmlAction::class)->execute()->canConvert($this->contentItem->html)) {
            throw CouldNotSendCampaign::invalidMjml($this);
        }
    }

    public function markAsSent(int $numberOfSubscribers): self
    {
        $this->update([
            'status' => CampaignStatus::Sent,
            'sent_at' => now(),
        ]);

        $this->contentItem->update([
            'statistics_calculated_at' => now(),
            'sent_to_number_of_subscribers' => $numberOfSubscribers,
        ]);

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return $this->isSent();
    }

    public function sendTestMail(string|array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch_sync(new SendCampaignTestJob($this, $email));
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.campaign.webview', $this->uuid));
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    public function toFeedItem(): FeedItem
    {
        return (new FeedItem())
            ->authorName('Mailcoach')
            ->authorEmail($this->contentItem->from_email ?? '')
            ->link($this->webviewUrl())
            ->title($this->contentItem->subject)
            ->id($this->uuid)
            ->summary('')
            ->updated($this->sent_at);
    }

    public function emailListSubscriberCount(): int
    {
        if (! $this->emailList) {
            return 0;
        }

        return $this->emailList->totalSubscriptionsCount();
    }

    public function sendsCount(): int
    {
        return $this->contentItem->sentSends()->count();
    }

    public function sendsWithoutInvalidated(): HasMany
    {
        return $this->contentItem->sends()->whereNull('invalidated_at');
    }

    public function wasSentToAllSubscribers(): bool
    {
        if (! $this->isSent()) {
            return false;
        }

        return $this->contentItem->sends()->pending()->count() === 0;
    }

    protected function ensureUpdatable(): void
    {
        if ($this->isSending()) {
            throw CouldNotUpdateCampaign::beingSent($this);
        }

        if ($this->isSent()) {
            throw CouldNotUpdateCampaign::alreadySent($this);
        }

        if ($this->isCancelled()) {
            throw CouldNotSendCampaign::cancelled($this);
        }
    }

    protected function markAsSending(): self
    {
        $this->update([
            'status' => CampaignStatus::Sending,
        ]);

        return $this;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatus::Draft;
    }

    public function isPreparing(): bool
    {
        return $this->isSending() && ! $this->sent_to_number_of_subscribers;
    }

    public function isSending(): bool
    {
        return $this->status == CampaignStatus::Sending;
    }

    public function isSent(): bool
    {
        return $this->status == CampaignStatus::Sent;
    }

    public function isSendingOrSent(): bool
    {
        return $this->isSending() || $this->isSent();
    }

    public function isCancelled(): bool
    {
        return $this->status == CampaignStatus::Cancelled;
    }

    public function getSummary(): string
    {
        $html = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $this->contentItem->webview_html);

        $converter = new HtmlConverter([
            'strip_tags' => true,
            'suppress_errors' => false,
            'remove_nodes' => 'head script style img code hr',
        ]);

        try {
            $text = $converter->convert($html);
            $text = app(RenderMarkdownToHtmlAction::class)->execute($text);
        } catch (Throwable) {
            $text = $html;
        }

        $text = strip_tags($text, ['p', 'strong', 'em', 'b', 'i', 'br']);

        $text = preg_replace('/=+/', '', $text);

        return Str::limit($text, 300);
    }

    public function allSendsCreated(): bool
    {
        return ! is_null($this->all_sends_created_at);
    }

    public function markAsAllSendsCreated(): self
    {
        $this->update([
            'all_sends_created_at' => now(),
        ]);

        return $this;
    }

    public function allMailSendingJobsDispatched(): bool
    {
        return ! is_null($this->all_sends_dispatched_at);
    }

    public function markAsAllMailSendingJobsDispatched(): self
    {
        $this->update([
            'all_sends_dispatched_at' => now(),
        ]);

        return $this;
    }

    protected static function newFactory(): CampaignFactory
    {
        return new CampaignFactory();
    }

    public function websiteUrl(): string
    {
        return route('mailcoach.website.campaign', [$this->emailList->website_slug, $this->uuid]);
    }

    public function websiteSummary(): ?string
    {
        if (! $this->contentItem->webview_html) {
            return null;
        }

        $document = app(CreateDomDocumentFromHtmlAction::class)->execute($this->contentItem->webview_html);

        $preheader = $document->getElementById('preheader');

        if (! $preheader) {
            return null;
        }

        return $preheader->textContent;
    }

    public static function defaultReplacers(): Collection
    {
        return collect([
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebsiteUrlCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebsiteCampaignUrlCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebviewCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\SubscriberReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\EmailListCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\UnsubscribeUrlReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignNameCampaignReplacer::class,
        ]);
    }

    public static function defaultActions(): Collection
    {
        return collect([
            'retry_sending_failed_sends' => \Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction::class,
            'send_campaign' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction::class,
            'send_campaign_mails' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction::class,
            'send_test_mail' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignTestAction::class,
            'validate_campaign_requirements' => \Spatie\Mailcoach\Domain\Campaign\Actions\ValidateCampaignRequirementsAction::class,
        ]);
    }
}
