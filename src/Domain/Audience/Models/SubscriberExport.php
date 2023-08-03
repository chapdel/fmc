<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\SubscriberExportFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberExportStatus;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubscriberExport extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasUuid;
    use HasFactory;
    use MassPrunable;
    use UsesMailcoachModels;

    public $table = 'mailcoach_subscriber_exports';

    public $guarded = [];

    protected $casts = [
        'exported_subscribers_count' => 'integer',
        'status' => SubscriberExportStatus::class,
        'errors' => 'array',
        'filters' => 'json',
    ];

    public static function booted()
    {
        static::creating(function (SubscriberExport $subscriberExport) {
            if (empty($subscriberExport->status)) {
                $subscriberExport->status = SubscriberExportStatus::Pending;
            }
        });
    }

    public function addError(string $message): void
    {
        $errors = $this->errors;
        $errors[] = $message;
        $this->errors = $errors;
        $this->save();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('file')
            ->useDisk(config('mailcoach.audience.export_subscribers_disk'))
            ->singleFile();
    }

    protected static function newFactory(): SubscriberExportFactory
    {
        return new SubscriberExportFactory();
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subWeek());
    }
}
