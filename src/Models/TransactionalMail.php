<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionalMail extends Model
{
    public $table = 'mailcoach_transactional_mails';

    public $guarded = [];

    public $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
    ];
}
