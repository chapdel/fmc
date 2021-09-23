<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Trigger extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_triggers';

    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            cache()->forget('automation-triggers');
        });
    }

    public function setTriggerAttribute(AutomationTrigger $value)
    {
        $this->attributes['trigger'] = serialize($value);
    }

    public function getAutomationTrigger(): AutomationTrigger
    {
        return $this->trigger;
    }

    public function getTriggerAttribute(string $value): AutomationTrigger
    {
        /** @var AutomationTrigger $trigger */
        $trigger = unserialize($value);
        $trigger->uuid = $this->uuid;
        $trigger->setAutomation($this->automation);

        return $trigger;
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationClass());
    }
}
