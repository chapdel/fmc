<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotStartAutomation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Automation extends Model
{
    use HasUuid, UsesMailcoachModels, SendsToSegment;

    public $table = 'mailcoach_automations';

    protected $guarded = [];

    protected $casts = [
        'run_at' => 'datetime',
    ];

    public function setTriggerAttribute(AutomationTrigger $value)
    {
        $this->attributes['trigger'] = serialize($value);
    }

    public function getTriggerAttribute(?string $value)
    {
        return unserialize($value);
    }

    public static function booted()
    {
        static::creating(function (Automation $automation) {
            if (! $automation->status) {
                $automation->status = AutomationStatus::PAUSED;
            }
        });

        static::saved(function () {
            cache()->forget('mailcoach-automations');
        });
    }

    public function name(string $name): self
    {
        $this->update(compact('name'));

        return $this;
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class)->whereNull('parent_id')->orderBy('order');
    }

    public function allActions(): HasMany
    {
        return $this->hasMany(Action::class)->orderBy('order');
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo($this->getEmailListClass());
    }

    public function newSubscribersQuery(): Builder
    {
        $subscribersQuery = $this->baseSubscribersQuery();
        $segment = $this->getSegment();
        $segment->subscribersQuery($subscribersQuery);

        return $subscribersQuery;
    }

    public function to(EmailList $emailList): self
    {
        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function trigger(AutomationTrigger $trigger): self
    {
        $this->update(['trigger' => $trigger]);

        return $this;
    }

    public function runEvery(CarbonInterval $interval): self
    {
        $this->update(['interval' => $interval]);

        return $this;
    }

    public function chain(array $chain): self
    {
        $newActions = collect($chain);

        $this->actions()->each(function ($existingAction) use ($newActions) {
            if (! $newActions->pluck('uuid')->contains($existingAction->uuid)) {
                $existingAction->delete();
            }
        });

        $newActions->each(function ($action, $index) {
            if (! $action instanceof AutomationAction) {
                $uuid = $action['uuid'];
                /** @var \Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction $action */
                $action = $action['class']::make($action['data']);
                $action->uuid = $uuid;
            }

            $action->store($action->uuid ?? Str::uuid()->toString(), $this, $index);
        });

        return $this->fresh('actions');
    }

    public function start(): self
    {
        if (! $this->interval) {
            throw CouldNotStartAutomation::noInterval($this);
        }

        if (! $this->emailList()->count() > 0) {
            throw CouldNotStartAutomation::noListSet($this);
        }

        if (! $this->trigger) {
            throw CouldNotStartAutomation::noTrigger($this);
        }

        if (! $this->actions()->count() > 0) {
            throw CouldNotStartAutomation::noActions($this);
        }

        if ($this->status === AutomationStatus::STARTED) {
            throw CouldNotStartAutomation::started($this);
        }

        $this->update(['status' => AutomationStatus::STARTED]);

        return $this;
    }

    public function run(Subscriber $subscriber): void
    {
        $this->actions()->first()->subscribers()->attach($subscriber);
    }
}
