<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionalMailTemplate extends Model
{
    use HasFactory;

    public $casts = [
        'store_mail' => 'boolean',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
    ];
}
