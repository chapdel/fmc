<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;

class TransactionalMail extends Model
{
    use HasFactory;

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

    public function opens(): HasManyThrough
    {
        return $this->hasManyThrough(
            TransactionalMailOpen::class,
            Send::class,
            'transactional_mail_id'
        );
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(
            TransactionalMailClick::class,
            Send::class,
            'transactional_mail_id'
        );
    }

    public function clicksPerUrl(): Collection
    {
        return DB::table($this->table)
            ->where('send_id', $this->send_id)
            ->groupBy('url')
            ->get();
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
