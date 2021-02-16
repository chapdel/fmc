<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class CampaignClick extends Model
{
    use HasFactory;

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
