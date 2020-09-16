<?php

namespace Spatie\Mailcoach\Actions\Templates;

use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Template
    {
        return $this->getTemplateClass()::create([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);
    }
}
