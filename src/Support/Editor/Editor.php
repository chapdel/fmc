<?php

namespace Spatie\Mailcoach\Support\Editor;

use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;

interface Editor
{
    public function render(HasHtmlContent $model): string;
}
