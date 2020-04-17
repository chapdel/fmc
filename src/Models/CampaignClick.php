<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    public $table = 'mailcoach_campaign_clicks';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class, 'send_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(CampaignLink::class, 'campaign_link_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.subscriber'), 'subscriber_id');
    }
}
