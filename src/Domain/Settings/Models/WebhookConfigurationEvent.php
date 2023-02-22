<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookConfigurationEvent extends Model
{
    use UsesMailcoachModels;
    use HasUuid;

    public $guarded = [];

    public $table = 'mailcoach_webhook_configuration_events';
}
