<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailClick extends Model
{
    use HasFactory;

    public $table = 'mailcoach_automation_mail_clicks';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class, 'send_id');
    }
}
