<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;

class Action extends Model
{
    use HasUuid;
    use HasFactory;

    public $table = 'mailcoach_automation_actions';

    protected $guarded = [];

    protected $casts = [
        'order' => 'int',
    ];

    public function setActionAttribute(AutomationAction $value)
    {
        $this->attributes['action'] = serialize($value);
    }

    public function getActionAttribute(string $value): AutomationAction
    {
        /** @var AutomationAction $action */
        $action = unserialize($value);
        $action->uuid = $this->uuid;

        return $action;
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'mailcoach_automation_action_subscriber')
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->withTimestamps();
    }

    public function activeSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->wherePivotNull('halted_at')
            ->wherePivotNull('run_at');
    }

    public function completedSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->wherePivotNotNull('run_at');
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Action::class, 'parent_id')->orderBy('order');
    }

    public function next(): ?Action
    {
        return $this->automation->actions->where('order', '>', $this->order)->first();
    }

    public function toLivewireArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'class' => get_class($this->action),
            'data' => $this->action->toArray(),
            'active' => (int) ($this->active_subscribers_count ?? 0),
            'completed' => (int) ($this->completed_subscribers_count ?? 0),
        ];
    }

    public function run()
    {
        $this->subscribers()
            ->wherePivotNull('halted_at')
            ->wherePivotNull('completed_at')
            ->cursor()
            ->each(function (Subscriber $subscriber) {
                dispatch(new RunActionForSubscriberJob($this, $subscriber));
            });
    }
}
