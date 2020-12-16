<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Models\Concerns\HasUuid;

class Action extends Model
{
    use HasUuid, HasFactory;

    public $table = 'mailcoach_automation_actions';

    protected $guarded = [];

    protected $casts = [
        'order' => 'int',
    ];

    public function setActionAttribute(AutomationAction $value)
    {
        $this->attributes['action'] = serialize($value);
    }

    public function getActionAttribute(string $value)
    {
        return unserialize($value);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'mailcoach_automation_action_subscriber')->withTimestamps();
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
        return $this->hasMany(Action::class, 'parent_id');
    }

    public function next(): ?Action
    {
        return $this->automation->actions->where('order', '>', $this->order)->first();
    }

    public function run()
    {
        $this->subscribers()
            ->wherePivotNull('run_at')
            ->each(function (Subscriber $subscriber) {
                /** @var AutomationAction $action */
                $action = $this->action;
                $action->run($subscriber);

                if ($action->shouldHalt($subscriber)) {
                    $this->subscribers()->detach($subscriber);

                    return;
                }

                if (! $action->shouldContinue($subscriber)) {
                    return;
                }

                $this->subscribers()->updateExistingPivot($subscriber, ['run_at' => now()], false);

                if ($this->next()) {
                    $this->next()->subscribers()->attach($subscriber);
                    $this->subscribers()->detach($subscriber);
                }
            });
    }
}
