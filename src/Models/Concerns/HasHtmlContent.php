<?php

namespace Spatie\Mailcoach\Models\Concerns;

interface HasHtmlContent
{
    public function getHtml(): ?string;

    public function getStructuredHtml(): ?string;
}
