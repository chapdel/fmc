<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class AutomationMail extends Model
{
    use HasFactory;

    public $table = 'mailcoach_automation_mails';

    public $guarded = [];


    public function opens(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                AutomationMailOpen::class,
                Send::class,
                'transactional_mail_id'
            )
            ->orderBy('created_at');
    }

    public function clicks(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                AutomationMailClick::class,
                Send::class,
                'transactional_mail_id'
            )
            ->orderBy('created_at');
    }
}
