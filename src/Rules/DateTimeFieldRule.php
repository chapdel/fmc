<?php

namespace Spatie\Mailcoach\Rules;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class DateTimeFieldRule implements Rule
{
    private string $message;

    public function passes($attribute, $value)
    {
        $this->message = __('Invalid date time provided.');

        if (! is_array($value)) {
            return false;
        }

        foreach (['date', 'hours', 'minutes'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $value)) {
                $this->message = [
                    'date' => __('Date key is missing'),
                    'hours' => __('Hours key is missing'),
                    'minutes' => __('Minutes key is missing'),
                ][$requiredKey];

                return false;
            }
        }

        $dateTime = $this->parseDateTime($value);

        if (! $dateTime) {
            return false;
        }

        if (! $dateTime->isFuture()) {
            $this->message = __('Date time must be in the future.');

            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }

    public function parseDateTime(array $value): ?CarbonInterface
    {
        try {
            $hours = str_pad($value['hours'], 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($value['minutes'], 2, '0', STR_PAD_LEFT);

            return Carbon::createFromFormat(
                'Y-m-d H:i',
                "{$value['date']} {$hours}:{$minutes}"
            );
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }
}
