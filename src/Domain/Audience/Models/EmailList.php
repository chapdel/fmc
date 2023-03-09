<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Manipulations;
use Spatie\Mailcoach\Database\Factories\EmailListFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EmailList extends Model implements HasMedia
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;
    use InteractsWithMedia;

    public $guarded = [];

    public $table = 'mailcoach_email_lists';

    public $casts = [
        'requires_confirmation' => 'boolean',
        'allow_form_subscriptions' => 'boolean',
        'report_campaign_sent' => 'boolean',
        'report_campaign_summary' => 'boolean',
        'report_email_list_summary' => 'boolean',
        'email_list_summary_sent_at' => 'datetime',
        'campaigns_feed_enabled' => 'boolean',
        'has_website' => 'boolean',
        'show_subscription_form_on_website' => 'boolean',
    ];

    public function subscribers(): HasMany
    {
        return $this->allSubscribers()->subscribed();
    }

    public function allSubscribers(): HasMany
    {
        return $this->hasMany(self::getSubscriberClass(), 'email_list_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(self::getCampaignClass(), 'email_list_id');
    }

    public function subscriberImports(): HasMany
    {
        return $this->hasMany(self::getSubscriberImportClass(), 'email_list_id');
    }

    public function confirmationMail(): BelongsTo
    {
        return $this->belongsTo(self::getTransactionalMailClass(), 'confirmation_mail_id');
    }

    public function tags(): HasMany
    {
        return $this
            ->hasMany(self::getTagClass(), 'email_list_id')
            ->orderBy('name');
    }

    public function segments()
    {
        return $this->hasMany(self::getTagSegmentClass(), 'email_list_id');
    }

    public function scopeSummarySentMoreThanDaysAgo(Builder $query, int $days)
    {
        $query
            ->where('email_list_summary_sent_at', '<=', now()->subDays($days)->toDateTimeString());
    }

    public function allowedFormSubscriptionTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_email_list_allow_form_subscription_tags', 'email_list_id', 'tag_id')
            ->orderBy('name');
    }

    public function setFormExtraAttributesAttribute($value)
    {
        $this->attributes['allowed_form_extra_attributes'] = array_map('trim', explode(',', $value));
    }

    public function allowedFormExtraAttributes(): array
    {
        return array_filter(array_map('trim', explode(',', trim($this->allowed_form_extra_attributes))));
    }

    public function subscribe(string $email, array $attributes = []): Subscriber
    {
        return self::getSubscriberClass()::createWithEmail($email, $attributes)->subscribeTo($this);
    }

    public function subscribeSkippingConfirmation(string $email, array $attributes = []): Subscriber
    {
        return self::getSubscriberClass()::createWithEmail($email, $attributes)->skipConfirmation()->subscribeTo($this);
    }

    public function isSubscribed(string $email): bool
    {
        if (! $subscriber = self::getSubscriberClass()::findForEmail($email, $this)) {
            return false;
        }

        return $subscriber->isSubscribed();
    }

    public function unsubscribe(string $email): bool
    {
        if (! $subscriber = self::getSubscriberClass()::findForEmail($email, $this)) {
            return false;
        }

        $subscriber->unsubscribe();

        return true;
    }

    public function getSubscriptionStatus(string $email): ?SubscriptionStatus
    {
        if (! $subscriber = self::getSubscriberClass()::findForEmail($email, $this)) {
            return null;
        }

        return $subscriber->status;
    }

    public function feedUrl(): string
    {
        return route('mailcoach.feed', $this->uuid);
    }

    public function incomingFormSubscriptionsUrl(): string
    {
        return route('mailcoach.subscribe', $this->uuid);
    }

    public function confirmSubscriberMailableClass(): string
    {
        return empty($this->confirmation_mailable_class)
            ? ConfirmSubscriberMail::class
            : $this->confirmation_mailable_class;
    }

    public function hasCustomizedConfirmationMailFields(): bool
    {
        if (! empty($this->confirmation_mail_id)) {
            return true;
        }

        return false;
    }

    public function campaignReportRecipients(): array
    {
        if (empty($this->report_recipients)) {
            return [];
        }

        $recipients = explode(',', $this->report_recipients);

        return array_map('trim', $recipients);
    }

    public function summarize(CarbonInterface $summaryStartDateTime): array
    {
        return [
            'total_number_of_subscribers' => $this->totalSubscriptionsCount(),
            'total_number_of_subscribers_gained' => $this
                ->allSubscribers()
                ->where('subscribed_at', '>', $summaryStartDateTime->toDateTimeString())
                ->count(),
            'total_number_of_unsubscribes_gained' => $this
                ->allSubscribers()->unsubscribed()
                ->where('unsubscribed_at', '>', $summaryStartDateTime->toDateTimeString())
                ->count(),
        ];
    }

    public function totalSubscriptionsCount(): int
    {
        return Cache::remember(self::class . 'totalSubscriptionsCount' . $this->id, now()->addSeconds(30), fn () => $this->subscribers()->count());
    }

    public function allSubscriptionsCount(): int
    {
        return Cache::remember(self::class . 'allSubscriptionsCount' . $this->id, now()->addSeconds(30), fn () => $this->allSubscribers()->count());
    }

    public function unconfirmedCount(): int
    {
        return Cache::remember(self::class . 'unconfirmedCount' . $this->id, now()->addSeconds(30), fn () => $this->allSubscribers()->unconfirmed()->count());
    }

    public function unsubscribedCount(): int
    {
        return Cache::remember(self::class . 'unsubscribedCount' . $this->id, now()->addSeconds(30), fn () => $this->allSubscribers()->unsubscribed()->count());
    }

    protected static function newFactory(): EmailListFactory
    {
        return new EmailListFactory();
    }

    public function webhookConfigurations(): Collection
    {
        return $this->getWebhookConfigurationClass()::query()
            ->where('use_for_all_lists', true)
            ->orWhereHas('emailLists', function (EloquentBuilder $query) {
                $query->where('email_list_id', $this->id);
            })
            ->get();
    }

    public function websiteUrl(): string
    {
        if (! $this->has_website) {
            return '';
        }

        return route('mailcoach.website', ltrim($this->website_slug, '/'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('header')
            ->nonQueued()
            ->fit(Manipulations::FIT_MAX, 2000, 1000)
            ->keepOriginalImageFormat()
            ->sharpen(10);

        $this
            ->addMediaConversion('favicon')
            ->fit(Manipulations::FIT_MAX, 64, 64)
            ->format('png');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('header')
            ->singleFile();
    }

    public function websiteHeaderImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('header', 'header');
    }

    public function getSubscriptionFormHtml(): string
    {
        $url = $this->incomingFormSubscriptionsUrl();

        $honeyPot = $this->honeypot_field
            ? <<<html

                <!--
                    This is the honeypot field, this should be invisible to users
                    when filled in, the subscriber won't be created but will still
                    receive a "successfully subscribed" page to fool spam bots.
                -->
                <input type="text" name="{$this->honeypot_field}" style="display: none; tab-index: -1;">

            html
            : '';

        $attributes = '';

        foreach ($this->allowedFormExtraAttributes() as $attribute) {
            $attributes .= <<<html
                <input type="hidden" name="attributes[{$attribute}]" value="your-value" />

            html;
        }

        if ($attributes) {
            $comment = <<<'html'
                <!--
                    You can add extra attributes that you have allowed in the
                    onboarding settings of your email list by adding hidden
                    inputs with the correct attributes[attributeName] name
                -->
            html;

            $attributes = "\n\n".$comment."\n".$attributes;
        }

        return <<<html
        <form
            action="{$url}"
            method="post"
        >
            <input type="email" name="email" placeholder="Your email address" />
            {$honeyPot}
            <!--
                Optional: include any tags. Create them first on the "Tags" section.
                And make sure to allow them in the email list settings
            -->
            <input type="hidden" name="tags" value="tag 1;tag 2" />
            {$attributes}
            <input type="submit" value="Subscribe">
        </form>
        html;
    }
}
