<?php

namespace Spatie\Mailcoach\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Enums\SendFeedbackType;
use Spatie\Mailcoach\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Events\ComplaintRegisteredEvent;
use Spatie\Mailcoach\Models\Concerns\HasUuid;

class Send extends Model
{
    use HasUuid;

    public $table = 'mailcoach_sends';

    public $guarded = [];

    public $dates = [
        'sent_at',
        'failed_at',
    ];

    public static function findByTransportMessageId(string $transportMessageId): ?Model
    {
        return static::where('transport_message_id', $transportMessageId)->first();
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.subscriber'), 'subscriber_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.campaign'), 'campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class, 'send_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class, 'send_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(SendFeedbackItem::class, 'send_id');
    }

    public function latestFeedback(): ?SendFeedbackItem
    {
        return $this->feedback()->latest()->first();
    }

    public function bounces(): HasMany
    {
        return $this
            ->hasMany(SendFeedbackItem::class)
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasMany
    {
        return $this
            ->hasMany(SendFeedbackItem::class)
            ->where('type', SendFeedbackType::COMPLAINT);
    }

    public function markAsSent()
    {
        $this->sent_at = now();

        $this->save();

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return ! is_null($this->sent_at);
    }

    public function storeTransportMessageId(string $transportMessageId)
    {
        $this->update(['transport_message_id' => $transportMessageId]);

        return $this;
    }

    public function registerOpen(?DateTimeInterface $openedAt = null): ?CampaignOpen
    {
        if (! $this->campaign->track_opens) {
            return null;
        }

        if ($this->wasOpenedInTheLastSeconds(5)) {
            return null;
        }

        $campaignOpen = CampaignOpen::create([
            'send_id' => $this->id,
            'campaign_id' => $this->campaign->id,
            'subscriber_id' => $this->subscriber->id,
            'created_at' => $openedAt ?? now(),
        ]);

        event(new CampaignOpenedEvent($campaignOpen));

        $this->campaign->dispatchCalculateStatistics();

        return $campaignOpen;
    }

    protected function wasOpenedInTheLastSeconds(int $seconds): bool
    {
        $latestOpen = $this->opens()->latest()->first();

        if (! $latestOpen) {
            return false;
        }

        return $latestOpen->created_at->diffInSeconds() < $seconds;
    }

    public function registerClick(string $url, ?DateTimeInterface $clickedAt = null): ?CampaignClick
    {
        if (! $this->campaign->track_clicks) {
            return null;
        }

        if (Str::startsWith($url, route('mailcoach.unsubscribe', ''))) {
            return null;
        }

        $campaignLink = CampaignLink::firstOrCreate([
            'campaign_id' => $this->campaign->id,
            'url' => $url,
        ]);

        $campaignClick = $campaignLink->registerClick($this, $clickedAt);

        event(new CampaignLinkClickedEvent($campaignClick));

        $this->campaign->dispatchCalculateStatistics();

        return $campaignClick;
    }

    public function registerBounce(?DateTimeInterface $bouncedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::BOUNCE,
            'created_at' => $bouncedAt ?? now(),
        ]);

        $this->subscriber->unsubscribe($this);

        event(new BounceRegisteredEvent($this));

        return $this;
    }

    public function registerComplaint(?DateTimeInterface $complainedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::COMPLAINT,
            'created_at' => $complainedAt ?? now(),
        ]);

        $this->subscriber->unsubscribe($this);

        event(new ComplaintRegisteredEvent($this));

        return $this;
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('sent_at');
    }

    public function scopeSent(Builder $query): void
    {
        $query
            ->whereNotNull('sent_at')
            ->whereNull('failed_at');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->whereNotNull('failed_at');
    }

    public function scopeBounced(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::BOUNCE);
        });
    }

    public function scopeComplained(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::COMPLAINT);
        });
    }

    public function markAsFailed(string $failureReason): self
    {
        $this->update([
            'sent_at' => now(),
            'failed_at' => now(),
            'failure_reason' => $failureReason,
        ]);

        return $this;
    }

    public function prepareRetryAfterFailedSend()
    {
        $this->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);
    }
}
