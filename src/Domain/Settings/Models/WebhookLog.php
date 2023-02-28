<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookLog extends Model
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;

    public $table = 'mailcoach_webhook_logs';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    public function webhookConfiguration()
    {
        return $this->belongsTo(self::getWebhookConfigurationClass(), 'webhook_configuration_id');
    }

    public function wasSuccessful(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }
}
