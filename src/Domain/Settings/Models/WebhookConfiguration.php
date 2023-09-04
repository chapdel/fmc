<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookDisabledMailAction;
use Spatie\Mailcoach\Domain\Settings\Enums\WebhookEventTypes;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @property string $name
 * @property string $url
 * @property bool $enabled
 * @property int $failed_attempts
 * @property Collection $events
 */
class WebhookConfiguration extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesMailcoachModels;

    public $guarded = [];

    public $table = 'mailcoach_webhook_configurations';

    public $casts = [
        'enabled' => 'boolean',
        'use_for_all_lists' => 'boolean',
        'secret' => 'encrypted',
        'use_for_all_events' => 'boolean',
        'events' => 'collection',
        'failed_attempts' => 'integer',
    ];

    protected $attributes = [
        'events' => '[]',
    ];

    public function emailLists(): BelongsToMany
    {
        return $this->belongsToMany(
            self::getEmailListClass(),
            'mailcoach_webhook_configuration_email_lists',
            'webhook_configuration_id',
            'email_list_id',
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(self::getWebhookLogClass());
    }

    public function useForAllEvents(): bool
    {
        if ($this->selectableEventsEnabled()) {
            return $this->use_for_all_events;
        }

        return true;
    }

    public function selectableEventsEnabled(): bool
    {
        return config('mailcoach.webhooks.selectable_event_types_enabled', false);
    }

    public function logsEnabled(): bool
    {
        return config('mailcoach.webhooks.logs', false);
    }

    public function countSelectableEventTypes(): int
    {
        return count(WebhookEventTypes::cases());
    }

    public function maximumAttempts(): ?int
    {
        return config('mailcoach.webhooks.maximum_attempts', 5);
    }

    public function resetFailedAttempts(): void
    {
        if ($this->failed_attempts > 0) {
            $this->update(['failed_attempts' => 0]);
        }
    }

    public function incrementFailedAttempts(): void
    {
        if (! $this->enabled) {
            return;
        }

        if ($this->failed_attempts < $this->maximumAttempts()) {
            $this->increment('failed_attempts');
        }

        if ($this->failed_attempts >= $this->maximumAttempts()) {
            $this->update(['enabled' => false]);

            resolve(SendWebhookDisabledMailAction::class)->execute($this);
        }
    }
}
