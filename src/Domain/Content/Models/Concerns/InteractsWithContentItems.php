<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait InteractsWithContentItems
{
    public static function bootInteractsWithContentItems(): void
    {
        static::created(function (HasContentItems $model) {
            if (! $model->contentItem) {
                $contentItem = $model->contentItem()->firstOrCreate();
                $model->setRelation('contentItem', $contentItem);
            }
        });

        static::deleting(function (HasContentItems $model) {
            $model->contentItem->delete();
        });
    }

    public function contentItem(): MorphOne
    {
        return $this->morphOne(self::getContentItemClass(), 'model');
    }

    public function contentItems(): MorphMany
    {
        return $this->morphMany(self::getContentItemClass(), 'model');
    }
}
