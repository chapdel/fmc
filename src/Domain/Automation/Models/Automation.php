<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotStartAutomation;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class Automation extends Model
{
    use HasUuid, UsesMailcoachModels, SendsToSegment;

    public $table = 'mailcoach_automations';

    protected $guarded = [];

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

    public function interval(CarbonInterval $interval): self
    {
        $this->update(['interval' => $interval]);

        return $this;
    }

    public function chain(array $chain): self
    {
        $newChain = array_filter($chain, function ($action) {
            return $action instanceof AutomationAction;
        });

        foreach ($newChain as $index => $newAction) {
            $oldAction = $this->actions()->where('uuid', $newAction->uuid)->first();

            if ($oldAction) {
                $oldAction->update(['order' => $index]);
            } else {
                $this->addAction($newAction, $index);
            }
        }

        foreach ($this->actions()->whereNull('parent_id')->get() as $oldAction) {
            if (! in_array(serialize($oldAction->action), array_map('serialize', $newChain))) {
                $oldAction->delete();
            }
        }

        return $this->fresh('actions');
    }

    public function addAction(AutomationAction $action, ?int $order = null)
    {
        $action->store($this, $order);

        return $this;
    }

    public function start(): self
    {
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
