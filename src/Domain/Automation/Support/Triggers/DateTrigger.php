<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;

class DateTrigger extends AutomationTrigger
{
    public CarbonInterface $date;

    public function __construct(CarbonInterface $date)
    {
        parent::__construct();

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
        return new self(Date::parse($data['date']));
    }

    public static function rules(): array
    {
        return [
            'date' => ['required', 'date'],
        ];
    }

    public function trigger(Automation $automation): void
    {
        if (! now()->startOfMinute()->equalTo($this->date->startOfMinute())) {
            return;
        }

        $this->fire($automation->newSubscribersQuery());
    }
}
