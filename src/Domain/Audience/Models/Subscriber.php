<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ParagonIE\CipherSweet\Backend\BoringCrypto;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedField;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Encryption\Transformation\EmailFirstPart;
use Spatie\Mailcoach\Domain\Audience\Encryption\Transformation\EmailSecondPart;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class Subscriber extends Model
{
    use HasUuid;
    use HasExtraAttributes;
    use UsesMailcoachModels;
    use HasFactory;

    public $table = 'mailcoach_subscribers';

    protected $guarded = [];

    public $casts = [
        'extra_attributes' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function getAttributes(): array
    {
        $attributes = parent::getAttributes();

        if (config('mailcoach.encryption.enabled') && isset($attributes['extra_attributes']) && str_starts_with($attributes['extra_attributes'], 'ey')) {
            $attributes['extra_attributes'] = self::$encrypter->decryptString($attributes['extra_attributes']);
        }

        return $attributes;
    }

    protected static function booted()
    {
        self::saving(function (self $subscriber) {
            if (! config('mailcoach.encryption.enabled') || ! $subscriber->isDirty(['email', 'first_name', 'last_name'])) {
                return;
            }

            $row = self::getEncryptedRow();

            [$fields, $indexes] = $row->prepareRowForStorage([
                'email' => $subscriber->email,
                'first_name' => $subscriber->first_name,
                'last_name' => $subscriber->last_name,
            ]);

            $subscriber->email = $fields['email'];
            $subscriber->email_idx_1 = $indexes['email_first_part'];
            $subscriber->email_idx_2 = $indexes['email_second_part'];

            $subscriber->first_name = $fields['first_name'];
            $subscriber->first_name_idx = $indexes['first_name'];

            $subscriber->last_name = $fields['last_name'];
            $subscriber->last_name_idx = $indexes['last_name'];
        });
    }

    protected function decrypt(): void
    {
        if (config('mailcoach.encryption.enabled') && str_starts_with($this->attributes['email'], app(CipherSweet::class)->getBackend()->getPrefix())) {
            $this->attributes = self::getEncryptedRow()->decryptRow($this->attributes);
        }
    }

    protected function getEmailAttribute(): string
    {
        $this->decrypt();

        return $this->attributes['email'];
    }

    protected function getFirstNameAttribute(): ?string
    {
        $this->decrypt();

        return $this->attributes['first_name'] ?? null;
    }

    protected function getLastNameAttribute(): ?string
    {
        $this->decrypt();

        return $this->attributes['last_name'] ?? null;
    }

    public static function getEncryptedRow(): EncryptedRow
    {
        $row = (new EncryptedRow(app(CipherSweet::class), self::getSubscriberTableName()))
            ->addTextField('email')
            ->addTextField('first_name')
            ->addTextField('last_name');

        $row->addBlindIndex('email', new BlindIndex('email_first_part', [new EmailFirstPart()]));
        $row->addBlindIndex('email', new BlindIndex('email_second_part', [new EmailSecondPart()]));

        $row->addBlindIndex('first_name', new BlindIndex('first_name'));
        $row->addBlindIndex('last_name', new BlindIndex('last_name'));

        return $row;
    }

    public static function createWithEmail(string $email, array $attributes = []): PendingSubscriber
    {
        return new PendingSubscriber($email, $attributes);
    }

    public static function findForEmail(string $email, EmailList $emailList): ?Subscriber
    {
        $query = static::query()->where('email_list_id', $emailList->id);

        if (config('mailcoach.encryption.enabled')) {
            $firstPart = self::getEncryptedRow()->getBlindIndex('email_first_part', ['email' => $email]);
            $secondPart = self::getEncryptedRow()->getBlindIndex('email_second_part', ['email' => $email]);

            return $query
                ->where('email_idx_1', $firstPart)
                ->where('email_idx_2', $secondPart)
                ->first();
        }

        return $query->where('email', $email)->first();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(self::getSendClass(), 'subscriber_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getCampaignOpenClass(), 'subscriber_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(self::getCampaignClickClass(), 'subscriber_id');
    }

    public function uniqueClicks(): HasMany
    {
        return $this->clicks()->groupBy('campaign_link_id')->addSelect('campaign_link_id');
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id')
            ->orderBy('name');
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(self::getAutomationActionClass(), self::getActionSubscriberTableName())
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->withTimestamps();
    }

    public function currentAction(Automation $automation): ?Action
    {
        return $this->currentActions($automation)->first();
    }

    public function currentActionClass(Automation $automation): ?string
    {
        if (! $action = $this->currentAction($automation)) {
            return null;
        }

        return $action->action::class;
    }

    public function currentActions(Automation $automation): Collection
    {
        return $this->actions()
            ->where('automation_id', $automation->id)
            ->wherePivotNull('completed_at')
            ->latest()
            ->get();
    }

    public function unsubscribe(Send $send = null)
    {
        $this->update(['unsubscribed_at' => now()]);

        if ($send) {
            if ($send->campaign_id) {
                static::getCampaignUnsubscribeClass()::firstOrCreate([
                    'campaign_id' => $send->campaign->id,
                    'subscriber_id' => $send->subscriber->id,
                ], ['uuid' => Str::uuid()]);

                $send->campaign->dispatchCalculateStatistics();
            }

            if ($send->automation_mail_id) {
                static::getAutomationMailUnsubscribeClass()::firstOrCreate([
                    'automation_mail_id' => $send->automationMail->id,
                    'subscriber_id' => $send->subscriber->id,
                ], ['uuid' => Str::uuid()]);

                $send->automationMail->dispatchCalculateStatistics();
            }
        }

        event(new UnsubscribedEvent($this, $send));

        return $this;
    }

    public function unsubscribeUrl(Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe', [$this->uuid, optional($send)->uuid]));
    }

    public function unsubscribeTagUrl(string $tag, Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe-tag', [$this->uuid, $tag, optional($send)->uuid]));
    }

    public function preferencesUrl(Send $send = null): string
    {
        return url(route('mailcoach.manage-preferences', [$this->uuid, optional($send)->uuid]));
    }

    public function getStatusAttribute(): SubscriptionStatus
    {
        if (! is_null($this->unsubscribed_at)) {
            return SubscriptionStatus::Unsubscribed;
        }

        if (! is_null($this->subscribed_at)) {
            return SubscriptionStatus::Subscribed;
        }

        return SubscriptionStatus::Unconfirmed;
    }

    public function confirm()
    {
        $action = Mailcoach::getAudienceActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

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

    public function scopeWithoutSendsForCampaign(Builder $query, Campaign $campaign)
    {
        return $query->whereDoesntHave('sends', function (Builder $query) use ($campaign) {
            $query->where('campaign_id', $campaign->id);
        });
    }

    public function addTag(string | iterable $name, ?TagType $type = null): self
    {
        $names = Arr::wrap($name);

        return $this->addTags($names, $type);
    }

    public function addTags(array $names, ?TagType $type = null)
    {
        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $tag = self::getTagClass()::firstOrCreate([
                'name' => $name,
                'email_list_id' => $this->emailList->id,
            ], [
                'uuid' => Str::uuid(),
                'type' => $type ?? TagType::Default,
            ]);

            $this->tags()->attach($tag);
            $this->tags->add($tag);

            event(new TagAddedEvent($this, $tag));
        }

        return $this;
    }

    public function hasTag(string $name): bool
    {
        return $this->tags
            ->where('name', $name)
            ->where('email_list_id', $this->emailList->id)
            ->count() > 0;
    }

    public function removeTag(string | array $name): self
    {
        $names = Arr::wrap($name);

        return $this->removeTags($names);
    }

    public function removeTags(array $names)
    {
        $tags = $this->tags()->whereIn('name', $names)->get();

        if ($tags->isEmpty()) {
            return $this;
        }

        foreach ($tags as $tag) {
            event(new TagRemovedEvent($this, $tag));
        }

        $this->tags()->detach($tags->pluck('id'));

        $this->load('tags');

        return $this;
    }

    public function syncTags(array $names, string $type = 'default')
    {
        $this->addTags($names);

        $this->tags()->where('type', $type)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', $type)->whereNotIn('name', $names)->pluck(self::getTagTableName() . '.id'));

        return $this;
    }

    public function syncPreferenceTags(array $names)
    {
        $this->addTags($names);

        $this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->pluck(self::getTagTableName() . '.id'));

        return $this->fresh('tags');
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
        return $this->status === SubscriptionStatus::Unconfirmed;
    }

    public function isSubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Subscribed;
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Unsubscribed;
    }

    public function inAutomation(Automation $automation): bool
    {
        return $this->actions()->where('automation_id', $automation->id)->count() > 0;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        /** Can also bind uuid */
        try {
            $subscriber = self::getSubscriberClass()::where('uuid', $value)->first();

            if ($subscriber) {
                return $subscriber;
            }
        } catch (\Illuminate\Database\QueryException) {
        }

        return self::getSubscriberClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): SubscriberFactory
    {
        return new SubscriberFactory();
    }
}
