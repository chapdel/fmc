<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Campaign\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Subscriber extends Model
{
    use HasUuid,
        HasExtraAttributes,
        UsesMailcoachModels,
        HasFactory;

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

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'mailcoach_automation_action_subscriber')
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->withTimestamps();
    }

    public function currentAction(Automation $automation): ?Action
    {
        return $this->actions()
            ->where('automation_id', $automation->id)
            ->wherePivotNull('completed_at')
            ->latest()
            ->first();
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
        return (string)url(route('mailcoach.unsubscribe', [$this->uuid, optional($send)->uuid]));
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
        $action = Config::getCampaignActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

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

    public function addTag(string|iterable $name, string $type = null): self
    {
        $names = Arr::wrap($name);

        return $this->addTags($names, $type);
    }

    public function addTags(array $names, string $type = null)
    {
        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $tag = Tag::firstOrCreate([
                'name' => $name,
                'email_list_id' => $this->emailList->id,
            ], [
                'type' => $type ?? TagType::DEFAULT,
            ]);

            $this->tags()->attach($tag);

            event(new TagAddedEvent($this, $tag));
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

    public function removeTag(string|array $name): self
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

    public function inAutomation(Automation $automation): bool
    {
        return $this->actions()->where('automation_id', $automation->id)->count() > 0;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getSubscriberClass()::where($field, $value)->firstOrFail();
    }
}
