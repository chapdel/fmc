<?php

namespace Spatie\Mailcoach\Actions\Templates;

use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class UpdateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(Template $template, array $attributes): Template
    {
        $template->update([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);

        return $template->fresh();
    }
}
