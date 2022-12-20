<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class RenderTwigAction
{
    public function execute(string $html, array $context = []): string
    {
        $twig = new Environment(new ArrayLoader(), [
            'autoescape' => 'html',
        ]);

        return $twig->createTemplate(urldecode($html))->render($context);
    }
}
