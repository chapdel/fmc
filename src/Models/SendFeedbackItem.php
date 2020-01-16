<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Enums\SendFeedbackType;

class SendFeedbackItem extends Model
{
    public $table = 'mailcoach_send_feedback_items';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class);
    }

    public function getFormattedTypeAttribute(): string
    {
        $formattedTypes = [
            SendFeedbackType::BOUNCE => 'Bounced',
            SendFeedbackType::COMPLAINT => 'Received complaint',
        ];

        return $formattedTypes[$this->type];
    }
}
