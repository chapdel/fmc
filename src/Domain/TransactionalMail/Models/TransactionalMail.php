<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;

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

    public function resend(): self
    {
        Mail::send(new ResendTransactionalMail($this));

        return $this;
    }

    public function toString(): string
    {
        return collect($this->to)
            ->map(function ($person) {
                return $person['email'];
            })
            ->implode(', ');
    }
}
