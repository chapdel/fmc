<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionalMailTemplate extends Model
{
    public $casts = [
        'store_mail' => 'boolean',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
    ];


}
