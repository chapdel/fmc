<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Spatie\Mailcoach\Domain\Content\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes, Template $template = null)
    {
        $html = $attributes['body'] ?? $template?->html;
        $structured_html = $template?->getStructuredHtml();

        return self::getTransactionalMailClass()::create([
            'name' => $attributes['name'],
            'type' => $attributes['type'],
            'template_id' => $template?->id,
            'body' => $html,
            'structured_html' => $structured_html,
        ]);
    }
}
