<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Campaign\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email_list_id' => Rule::exists($this->getEmailListTableName(), 'id'),
            'segment' => [Rule::in(['entire_list', 'segment'])],
            'segment_id' => ['required_if:segment,tag_segment'],
            'trigger' => ['required'],
        ];
    }

    public function getSegmentClass(): string
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $automation = $this->route()->parameter('automation');

        if ($automation->usingCustomSegment()) {
            return $automation->segment_class;
        }

        if ($this->segment === 'entire_list') {
            return EverySubscriberSegment::class;
        }

        return SubscribersWithTagsSegment::class;
    }

    public function emailList(): ?EmailList
    {
        if (! $this->email_list_id) {
            return null;
        }

        return $this->getEmailListClass()::find($this->email_list_id);
    }

    public function trigger(): AutomationTrigger
    {
        $triggerClass = config('mailcoach.automation.triggers')[$this->get('trigger')];

        return $triggerClass::createFromRequest($this);
    }
}
