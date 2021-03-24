<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailLink extends Model
{
    use HasFactory;

    public $table = 'mailcoach_automation_mail_links';

    public $casts = [
        'click_count' => 'integer',
        'unique_click_count' => 'integer',
    ];

    protected $guarded = [];

    public function automationMail(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.automation_mail'), 'automation_mail_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AutomationMailClick::class);
    }

    public function registerClick(Send $send, ?DateTimeInterface $clickedAt): AutomationMailClick
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick $automationMailClick */
        $automationMailClick = $this->clicks()->create([
            'send_id' => $send->id,
            'subscriber_id' => $send->subscriber->id,
            'created_at' => $clickedAt ?? now(),
        ]);

        $numberOfTimesClickedBySubscriber = $this->clicks()
            ->where('subscriber_id', $send->subscriber->id)
            ->count();

        if ($numberOfTimesClickedBySubscriber === 1) {
            $this->increment('unique_click_count');
        }

        $this->increment('click_count');

        return $automationMailClick;
    }
}
