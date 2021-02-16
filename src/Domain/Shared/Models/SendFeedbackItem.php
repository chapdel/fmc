<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class SendFeedbackItem extends Model
{
    use HasFactory;

    public $table = 'mailcoach_send_feedback_items';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class);
    }

    public function getFormattedTypeAttribute(): string
    {
        $formattedTypes = [
            SendFeedbackType::BOUNCE => __('Bounced'),
            SendFeedbackType::COMPLAINT => __('Received complaint'),
        ];

        return (string)$formattedTypes[$this->type] ?? '';
    }
}
