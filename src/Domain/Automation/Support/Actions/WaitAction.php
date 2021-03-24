<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class WaitAction extends AutomationAction
{
    public CarbonInterval $interval;

    public function __construct(CarbonInterval $interval)
    {
        parent::__construct();

        $this->interval = $interval;
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::pause();
    }

    public static function getName(): string
    {
        return (string) __('Wait for a duration');
    }

    public static function getComponent(): ?string
    {
        return 'wait-action';
    }

    public static function make(array $data): self
    {
        return new self(CarbonInterval::createFromDateString("{$data['length']} {$data['unit']}"));
    }

    public function toArray(): array
    {
        [$length, $unit] = explode(' ', $this->interval->forHumans());

        return [
            'length' => $length,
            'unit' => Str::plural($unit),
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
