<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

class TemplateRenderer
{
    public function __construct(protected string $html)
    {

    }

    public function containsPlaceHolders(): bool
    {
        return count($this->placeHolderNames()) > 0;
    }

    public function placeHolderNames(): array
    {
        preg_match_all('/\[\[\[(.*?)\]\]\]/', $this->html, $matches);

        return $matches[1];
    }

    public function render(array $values): string
    {
        $html = $this->html;

        foreach ($this->placeHolderNames() as $placeHolderName) {
            $html = str_replace(
                '[[[' . $placeHolderName . ']]]',
                $values[$placeHolderName] ?? '',
                $html,
            );
        }

        return $html;
    }
}
