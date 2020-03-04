<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class DateTimeFieldComponent extends Component
{
    public Carbon $value;

    public function __construct(?Carbon $value = null)
    {
        $this->value = $value ?? now();
    }

    public function hourOptions(): array
    {
        return collect(range(0, 23))->mapWithKeys(function (int $hour) {
            return [$hour => str_pad($hour, 2, '0', STR_PAD_LEFT)];
        })->toArray();
    }

    public function minuteOptions(): array
    {
        return collect(range(0, 60, 15))->mapWithKeys(function (int $minutes) {
            return [$minutes => str_pad($minutes, 2, '0', STR_PAD_LEFT)];
        })->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.form.dateTimeField');
    }
}
