<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionalMailClick extends Model
{
    use HasFactory;

    public $table = 'mailcoach_transactional_mail_clicks';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class, 'send_id');
    }
}
