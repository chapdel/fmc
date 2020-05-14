<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Rules\DateTimeFieldRule;

class ScheduleCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function getScheduledAt(): Carbon
    {
        return (new DateTimeFieldRule())->parseDateTime($this->scheduled_at);
    }
}
