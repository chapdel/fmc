<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class DateTimeFieldComponent extends Component
{
    public Carbon $value;

    public string $name;

    public function __construct(string $name, ?Carbon $value = null)
    {
        $this->value = $value ?? now();
        $this->name = $name;
    }

    public function hourOptions(): Collection
    {
        return collect(range(0, 23))->mapWithKeys(function (int $hour) {
            return [$hour => str_pad((string)$hour, 2, '0', STR_PAD_LEFT)];
        });
    }

    public function minuteOptions(): Collection
    {
        return collect(range(0, 60, 15))->mapWithKeys(function (int $minutes) {
            return [$minutes => str_pad((string)$minutes, 2, '0', STR_PAD_LEFT)];
        });
    }

    public function render()
    {
        return view('mailcoach::app.components.form.dateTimeField');
    }
}
