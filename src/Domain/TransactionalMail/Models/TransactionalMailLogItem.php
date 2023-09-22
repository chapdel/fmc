<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\TransactionalMailLogItemFactory;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;

class TransactionalMailLogItem extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesMailcoachModels;

    public $table = 'mailcoach_transactional_mail_log_items';

    public $guarded = [];

    public $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'fake' => 'boolean',
    ];

    public static function booted()
    {
        static::created(function (self $transactionalMailLogItem) {
            if (! $transactionalMailLogItem->contentItem) {
                $contentItem = $transactionalMailLogItem->contentItem()->firstOrCreate();
                $transactionalMailLogItem->setRelation('contentItem', $contentItem);
            }
        });
    }

    public function contentItem(): MorphOne
    {
        return $this->morphOne(self::getContentItemClass(), 'model');
    }

    public function getSend(): ?Send
    {
        return $this->contentItem->sends->first();
    }

    public function getSendAttribute(): ?Send
    {
        return $this->getSend();
    }

    public function clicksPerUrl(): Collection
    {
        return $this->contentItem
            ->clicks
            ->groupBy('link.url')
            ->map(function ($group, $url) {
                return [
                    'url' => $url,
                    'count' => $group->count(),
                    'first_clicked_at' => $group->first()->created_at,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    public function resend(): self
    {
        if (! $this->fake) {
            Mail::send(new ResendTransactionalMail($this));
        }

        return $this;
    }

    public function toString(): string
    {
        return collect($this->to)
            ->map(function ($person) {
                return $person['email'];
            })
            ->implode(', ');
    }

    protected static function newFactory(): TransactionalMailLogItemFactory
    {
        return new TransactionalMailLogItemFactory();
    }
}
