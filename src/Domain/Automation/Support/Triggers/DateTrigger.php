<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;

class DateTrigger extends AutomationTrigger
{
    public CarbonInterface $date;

    public function __construct(CarbonInterface $date)
    {
        $this->date = $date;
    }

    public static function getName(): string
    {
        return __('On a date');
    }

    public static function getComponent(): ?string
    {
        return 'date-trigger';
    }

    public static function make(array $data): self
    {
        return new self($data['date']);
    }

    public static function rules(): array
    {
        return [
            'date' => ['required', 'date'],
        ];
    }

    public static function createFromRequest(Request $request): AutomationTrigger
    {
        return new self(Date::parse($request->get('date')));
    }

    public function trigger(Automation $automation): void
    {
        if (! now()->isSameDay($this->date->startOfDay())) {
            return;
        }

        $this->fire($automation->newSubscribersQuery());
    }
}
