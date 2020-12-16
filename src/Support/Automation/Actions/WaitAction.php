<?php

namespace Spatie\Mailcoach\Support\Automation\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Models\Subscriber;

class WaitAction extends AutomationAction
{
    private CarbonInterval $interval;

    public function __construct(CarbonInterval $interval)
    {
        $this->interval = $interval;
    }

    public static function getName(): string
    {
        return __('Wait for a duration');
    }

    public function getDescription(): string
    {
        return $this->interval->cascade()->forHumans();
    }

    public static function getComponent(): ?string
    {
        return 'wait-action';
    }

    public static function make(array $data): self
    {
        return new self(CarbonInterval::createFromDateString($data['duration']));
    }

    public function toArray(): array
    {
        return [
            'duration' => $this->interval->forHumans(),
        ];
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        if ($subscriber->pivot->created_at <= now()->sub($this->interval)) {
            return true;
        }

        return false;
    }
}
