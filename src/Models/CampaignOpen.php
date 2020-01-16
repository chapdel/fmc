<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignOpen extends Model
{
    public $table = 'mailcoach_campaign_opens';

    protected $guarded = [];

    protected $casts = [
        'first_opened_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class, 'send_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }
}
