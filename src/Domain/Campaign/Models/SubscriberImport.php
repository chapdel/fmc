<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelCleanup\CleanupConfig;
use Spatie\ModelCleanup\GetsCleanedUp;

class SubscriberImport extends Model implements HasMedia, GetsCleanedUp
{
    use InteractsWithMedia, HasUuid, HasFactory;

    public $table = 'mailcoach_subscriber_imports';

    public $guarded = [];

    public static function booted()
    {
        static::creating(function (SubscriberImport $subscriberImport) {
            if (empty($subscriberImport->status)) {
                $subscriberImport->status = SubscriberImportStatus::PENDING;
            }
        });
    }

    protected $casts = [
        'imported_subscribers_count' => 'integer',
        'error_count' => 'integer',
        'mailcoach_email_lists_ids' => 'array',
        'replace_tags' => 'boolean',
    ];

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.email_list'), 'email_list_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('importFile')
            ->useDisk(config('mailcoach.campaigns.import_subscribers_disk'))
            ->singleFile();

        $this
            ->addMediaCollection('importedUsersReport')
            ->useDisk(config('mailcoach.campaigns.import_subscribers_disk'))
            ->singleFile();

        $this
            ->addMediaCollection('errorReport')
            ->useDisk(config('mailcoach.campaigns.import_subscribers_disk'))
            ->singleFile();
    }

    public function cleanUp(CleanupConfig $config): void
    {
        $config->olderThanDays(7);
    }
}
