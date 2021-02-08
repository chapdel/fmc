<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class UpdateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(TransactionalMailTemplate $template, array $attributes)
    {
        $template->update([
            'subject' => $attributes['subject'],
            'name' => $attributes['name'],
            'body' => $attributes['html'],
        ]);

        return $template->refresh();
    }
}
