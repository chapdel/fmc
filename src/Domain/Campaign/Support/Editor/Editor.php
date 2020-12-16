<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Editor;

use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;

interface Editor
{
    public function render(HasHtmlContent $model): string;
}
