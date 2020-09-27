<?php

namespace Spatie\Mailcoach\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Enums\SendFeedbackType;
use Spatie\Mailcoach\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Exceptions\CouldNotUpdateCampaign;
use Spatie\Mailcoach\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Concerns\CanBeScheduled;
use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Rules\HtmlRule;
use Spatie\Mailcoach\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Support\Segments\Segment;
use Spatie\Mailcoach\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Campaign extends Model implements Feedable, HasHtmlContent
{
    use CanBeScheduled,
        HasUuid,
        UsesMailcoachModels;

    public $table = 'mailcoach_campaigns';

    protected $guarded = [];

    public $casts = [
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'send_to_number_of_subscribers' => 'integer',
        'sent_at' => 'datetime',
        'requires_confirmation' => 'boolean',
        'statistics_calculated_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'campaigns_feed_enabled' => 'boolean',
        'last_modified_at' => 'datetime',
        'summary_mail_sent_at' => 'datetime',
    ];

    public static function booted()
    {
        static::creating(function (Campaign $campaign) {
            if (! $campaign->status) {
                $campaign->status = CampaignStatus::DRAFT;
            }
        });
    }

    public static function scopeSentBetween(Builder $query, CarbonInterface $periodStart, CarbonInterface $periodEnd): void
    {
        $query
            ->where('sent_at', '>=', $periodStart)
            ->where('sent_at', '<', $periodEnd);
    }

    public function scopeDraft(Builder $query): void
    {
        $query
            ->where('status', CampaignStatus::DRAFT)
            ->whereNull('scheduled_at')
            ->orderBy('created_at');
    }

    public function scopeSendingOrSent(Builder $query): void
    {
        $query->whereIn('status', [CampaignStatus::SENDING, CampaignStatus::SENT]);
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
        $query->where('status', CampaignStatus::SENT);
    }

    public function scopeSentDaysAgo(Builder $query, int $daysAgo)
    {
        $query
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now()->subDays($daysAgo)->toDateTimeString());
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.email_list'), 'email_list_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(CampaignLink::class, 'campaign_id');
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(CampaignClick::class, CampaignLink::class, 'campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class, 'campaign_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(Send::class, 'campaign_id');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(CampaignUnsubscribe::class, 'campaign_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, Send::class, 'campaign_id')
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, Send::class, 'campaign_id')
            ->where('type', SendFeedbackType::COMPLAINT);
    }

    public function tagSegment(): BelongsTo
    {
        return $this->belongsTo(TagSegment::class);
    }

    public function isReady(): bool
    {
        if (! $this->html) {
            return false;
        }

        if (! $this->hasValidHtml()) {
            return false;
        }

        if (! optional($this->emailList)->default_from_email) {
            return false;
        }

        if (! $this->subject) {
            return false;
        }

        if (! $this->emailListSubscriberCount()) {
            return false;
        }

        return true;
    }

    public function hasValidHtml(): bool
    {
        return (new HtmlRule())->passes('html', $this->html);
    }

    public function htmlContainsUnsubscribeUrlPlaceHolder(): bool
    {
        return Str::contains($this->html, '::unsubscribeUrl::');
    }

    public function isPending(): bool
    {
        return ! in_array($this->status, [
            CampaignStatus::SENDING,
            CampaignStatus::SENT,
        ]);
    }

    public function isScheduled(): bool
    {
        return $this->isDraft() && $this->scheduled_at;
    }

    public function from(string $email, string $name = null)
    {
        $this->update([
            'from_email' => $email,
            'from_name' => $name,
        ]);

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->ensureUpdatable();

        $this->update(compact('subject'));

        return $this;
    }

    public function trackOpens(bool $bool = true): self
    {
        $this->ensureUpdatable();

        $this->update(['track_opens' => $bool]);

        return $this;
    }

    public function trackClicks(bool $bool = true): self
    {
        $this->ensureUpdatable();

        $this->update(['track_clicks' => $bool]);

        return $this;
    }

    public function useMailable(string $mailableClass): self
    {
        $this->ensureUpdatable();

        if (! is_a($mailableClass, CampaignMail::class, true)) {
            throw CouldNotSendCampaign::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass]);

        return $this;
    }

    /**
     * @param \Spatie\Mailcoach\Support\Segments\Segment|string $segmentClassOrObject
     */
    public function segment($segmentClassOrObject): self
    {
        $this->ensureUpdatable();

        if (! is_a($segmentClassOrObject, Segment::class, true)) {
            throw CouldNotSendCampaign::invalidSegmentClass($this, $segmentClassOrObject);
        }

        $this->update(['segment_class' => serialize($segmentClassOrObject)]);

        return $this;
    }

    public function to(EmailList $emailList): self
    {
        $this->ensureUpdatable();

        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function content(string $html): self
    {
        $this->ensureUpdatable();

        $this->update(compact('html'));

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

    public function send(): self
    {
        $this->ensureSendable();

        if (empty($this->from_email)) {
            $this->from_email = $this->emailList->default_from_email;
            $this->save();
        }

        if (empty($this->from_name)) {
            $this->from_name = $this->emailList->default_from_name;
            $this->save();
        }

        $this->update([
            'segment_description' => $this->getSegment()->description($this),
            'last_modified_at' => now(),
        ]);

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        $this->markAsSending();

        dispatch(new SendCampaignJob($this));

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

        if (is_null($this->emailList)) {
            throw CouldNotSendCampaign::noListSet($this);
        }

        if ($this->hasCustomMailable()) {
            return;
        }

        if (empty($this->from_email) && empty($this->emailList->default_from_email)) {
            throw CouldNotSendCampaign::noFromEmailSet($this);
        }

        if (empty($this->subject)) {
            throw CouldNotSendCampaign::noSubjectSet($this);
        }

        if (empty($this->html)) {
            throw CouldNotSendCampaign::noContent($this);
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

    /**
     * @param string|array $emails
     */
    public function sendTestMail($emails)
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch(new SendTestMailJob($this, $email));
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.webview', $this->uuid));
    }

    public function getMailable(): CampaignMail
    {
        $mailableClass = $this->mailable_class ?? CampaignMail::class;

        return app($mailableClass);
    }

    public function getSegment(): Segment
    {
        $segmentClass = $this->segment_class ?? EverySubscriberSegment::class;

        $unserialized = @unserialize($segmentClass);
        if ($unserialized !== false) {
            $segmentClass = $unserialized;
        }

        if ($segmentClass instanceof Segment) {
            return $segmentClass->setCampaign($this);
        }

        return app($segmentClass)->setCampaign($this);
    }

    public function dispatchCalculateStatistics()
    {
        $lock = new CalculateStatisticsLock($this);

        if (! $lock->get()) {
            return;
        }

        dispatch(new CalculateStatisticsJob($this));
    }

    public function toFeedItem()
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

    public function baseSubscribersQuery(): Builder
    {
        return $this
            ->emailList
            ->subscribers()
            ->subscribed()
            ->getQuery();
    }

    public function segmentSubscriberCount(): int
    {
        if (! $this->emailList) {
            return 0;
        }

        return tap($this->baseSubscribersQuery(), function (Builder $query) {
            $this->getSegment()->subscribersQuery($query);
        })->count();
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
    }

    protected function markAsSending(): self
    {
        $this->update([
            'status' => CampaignStatus::SENDING,
        ]);

        return $this;
    }

    public function usesSegment(): bool
    {
        return $this->segment_class !== EverySubscriberSegment::class;
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

    public function segmentingOnSubscriberTags(): bool
    {
        return $this->segment_class === SubscribersWithTagsSegment::class;
    }

    public function notSegmenting(): bool
    {
        return is_null($this->segment_class)
            || $this->segment_class === EverySubscriberSegment::class;
    }

    public function usingCustomSegment(): bool
    {
        if (is_null($this->segment_class)) {
            return false;
        }

        return ! in_array($this->segment_class, [
            SubscribersWithTagsSegment::class,
            EverySubscriberSegment::class,
        ]);
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

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === CampaignMail::class) {
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

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getCampaignClass()::where($field, $value)->firstOrFail();
    }
}
