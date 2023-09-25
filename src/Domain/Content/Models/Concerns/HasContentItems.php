<?php

namespace Spatie\Mailcoach\Domain\Content\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasContentItems
{
    public function contentItem(): MorphOne;

    public function contentItems(): MorphMany;
}
