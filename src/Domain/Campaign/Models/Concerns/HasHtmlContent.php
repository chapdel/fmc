<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

interface HasHtmlContent
{
    public function getHtml(): ?string;

    public function getStructuredHtml(): ?string;

    public function getTemplateFieldValues(): array;

    public function setTemplateFieldValues(array $fieldValues = []): self;
}
