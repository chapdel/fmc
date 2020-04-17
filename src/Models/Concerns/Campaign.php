<?php

namespace Spatie\Mailcoach\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Support\Segments\Segment;

interface Campaign
{

    public function emailList(): BelongsTo;

    public function links(): HasMany;

    public function clicks(): HasManyThrough;

    public function opens(): HasMany;

    public function sends(): HasMany;

    public function unsubscribes(): HasMany;

    public function bounces(): HasManyThrough;

    public function complaints(): HasManyThrough;

    public function tagSegment(): BelongsTo;

    public function isReady(): bool;

    public function hasValidHtml(): bool;

    public function htmlContainsUnsubscribeUrlPlaceHolder(): bool;

    public function isPending(): bool;

    public function isScheduled(): bool;

    public function from(string $email, string $name = null);

    public function subject(string $subject): self;

    public function trackOpens(bool $bool = true): self;

    public function trackClicks(bool $bool = true): self;

    public function useMailable(string $mailableClass): self;

    /**
     * @param \Spatie\Mailcoach\Support\Segments\Segment|string $segmentClassOrObject
     */
    public function segment($segmentClassOrObject): self;

    public function to(EmailList $emailList): self;

    public function content(string $html): self;

    public function contentFromMailable(): string;

    public function send(): self;

    public function sendTo(EmailList $emailList): self;

    public function markAsSent(int $numberOfSubscribers): self;

    public function wasAlreadySent(): bool;

    /**
     * @param string|array $emails
     */
    public function sendTestMail($emails);

    public function webviewUrl(): string;

    public function getMailable(): CampaignMail;

    public function getSegment(): Segment;

    public function dispatchCalculateStatistics();

    public function toFeedItem();

    public function emailListSubscriberCount(): int;

    public function baseSubscribersQuery(): Builder;

    public function segmentSubscriberCount(): int;

    public function sendsCount(): int;

    public function wasSentToAllSubscribers(): bool;

    public function usesSegment(): bool;

    public function hasTroublesSendingOutMails(): bool;

    public function segmentingOnSubscriberTags(): bool;

    public function isDraft(): bool;

    public function isSending(): bool;

    public function isSent(): bool;

    public function htmlWithInlinedCss(): string;

    public function getHtml(): ?string;

    public function getStructuredHtml(): ?string;
}
