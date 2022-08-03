<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookConfiguration extends Model
{
    use HasUuid;
    use UsesMailcoachModels;

    public $table = 'mailcoach_webhook_configurations';

    public $casts = [
        'use_for_all_lists' => 'boolean',
    ];
}
