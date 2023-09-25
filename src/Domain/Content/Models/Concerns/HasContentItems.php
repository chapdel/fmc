<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property ?\Spatie\Mailcoach\Domain\Content\Models\ContentItem $contentItem
 * @property \Illuminate\Support\Collection<\Spatie\Mailcoach\Domain\Content\Models\ContentItem> $contentItems
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface HasContentItems
{
    public function contentItem(): MorphOne;

    public function contentItems(): MorphMany;
}
