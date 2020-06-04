<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\PendingSubscriber;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class Subscriber extends Model
{
    use HasUuid,
        HasExtraAttributes,
        UsesMailcoachModels;

    public $table = 'mailcoach_subscribers';

    public $casts = [
        'extra_attributes' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected $guarded = [];

    public static function createWithEmail(string $email, array $attributes = []): PendingSubscriber
    {
        return new PendingSubscriber($email, $attributes);
    }

    public static function findForEmail(string $email, EmailList $emailList): ?Subscriber
    {
        return static::where('email', $email)
            ->where('email_list_id', $emailList->id)
            ->first();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.email_list'), 'email_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(Send::class, 'subscriber_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class, 'subscriber_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class, 'subscriber_id');
    }

    public function uniqueClicks(): HasMany
    {
        return $this->clicks()->groupBy('campaign_link_id');
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id')
            ->orderBy('name');
    }

    public function unsubscribe(Send $send = null)
    {
        $this->update(['unsubscribed_at' => now()]);

        if ($send) {
            CampaignUnsubscribe::firstOrCreate([
                'campaign_id' => $send->campaign->id,
                'subscriber_id' => $send->subscriber->id,
            ]);

            $send->campaign->dispatchCalculateStatistics();
        }

        event(new UnsubscribedEvent($this, $send));

        return $this;
    }

    public function unsubscribeUrl(Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe', [$this->uuid, optional($send)->uuid]));
    }

    public function getStatusAttribute(): string
    {
        if (! is_null($this->unsubscribed_at)) {
            return SubscriptionStatus::UNSUBSCRIBED;
        }

        if (! is_null($this->subscribed_at)) {
            return SubscriptionStatus::SUBSCRIBED;
        }

        return SubscriptionStatus::UNCONFIRMED;
    }

    public function confirm()
    {
        $action = Config::getActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

        return $action->execute($this);
    }

    public function scopeUnconfirmed(Builder $query)
    {
        $query
            ->whereNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeSubscribed(Builder $query)
    {
        $query
            ->whereNotNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $query)
    {
        $query
            ->whereNotNull('unsubscribed_at');
    }

    /**
     * @param string|array $name
     *
     * @return self
     */
    public function addTag($name)
    {
        $names = Arr::wrap($name);

        return $this->addTags($names);
    }

    public function addTags(array $names)
    {
        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $tag = Tag::firstOrCreate([
                'name' => $name,
                'email_list_id' => $this->emailList->id,
            ]);

            $this->tags()->attach($tag);
        }

        return $this;
    }

    public function hasTag(string $name): bool
    {
        return $this->tags()
            ->where('name', $name)
            ->where('email_list_id', $this->emailList->id)
            ->exists();
    }

    /**
     * @param string|array $name
     *
     * @return self
     */
    public function removeTag($name)
    {
        $names = Arr::wrap($name);

        return $this->removeTags($names);
    }

    public function removeTags(array $names)
    {
        $this
            ->tags()
            ->detach($this->tags()->whereIn('name', $names)->pluck('mailcoach_tags.id'));

        return $this;
    }

    public function syncTags(array $names)
    {
        $this->addTags($names);

        $this->tags()->detach($this->tags()->whereNotIn('name', $names)->pluck('mailcoach_tags.id'));

        return $this;
    }

    public function toExportRow(): array
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'tags' => $this->tags()->pluck('name')->implode(";"),
        ];
    }

    public function isUnconfirmed(): bool
    {
        return $this->status === SubscriptionStatus::UNCONFIRMED;
    }

    public function isSubscribed(): bool
    {
        return $this->status === SubscriptionStatus::SUBSCRIBED;
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === SubscriptionStatus::UNSUBSCRIBED;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getSubscriberClass()::where($field, $value)->firstOrFail();
    }
}
