<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionalMail extends Model
{
    public $table = 'mailcoach_transactional_mails';

    public $guarded = [];

    public $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
    ];

    public function send(): HasOne
    {
        return $this->hasOne(Send::class, 'transactional_mail_id');
    }
}
