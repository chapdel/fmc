<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class WaitAction extends AutomationAction
{
    private CarbonInterval $interval;

    public function __construct(CarbonInterval $interval)
    {
        parent::__construct();

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
