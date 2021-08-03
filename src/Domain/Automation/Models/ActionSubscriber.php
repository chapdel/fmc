<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class ActionSubscriber extends Pivot
{
    public $table = 'mailcoach_automation_action_subscriber';

    public $incrementing = true;

    public $timestamps = true;

    protected $casts = [
        'run_at' => 'datetime',
        'completed_at' => 'datetime',
        'halted_at' => 'datetime',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
