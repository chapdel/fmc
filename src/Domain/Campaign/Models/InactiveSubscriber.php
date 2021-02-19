<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Campaign\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class InactiveSubscriber extends Model
{
    use UsesMailcoachModels,
        HasFactory;

    public $table = 'mailcoach_inactive_subscribers';

    public $casts = [
        'unsubscribe_at' => 'datetime',
    ];

    protected $guarded = [];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.subscriber'), 'subscriber_id');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getSubscriberClass()::where($field, $value)->firstOrFail();
    }
}
