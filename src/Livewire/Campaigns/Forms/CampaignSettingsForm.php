<?php

namespace Spatie\Mailcoach\Livewire\Campaigns\Forms;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class CampaignSettingsForm extends Form
{
    use UsesMailcoachModels;

    public Campaign $campaign;

    public string $name;

    public ?string $subject;

    public ?string $from_email;

    public ?string $from_name;

    public ?string $reply_to_email;

    public ?string $reply_to_name;

    public ?int $email_list_id;

    public bool $utm_tags;

    public bool $add_subscriber_tags;

    public bool $add_subscriber_link_tags;

    public ?int $segment_id;

    public bool $show_publicly;

    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->name = $campaign->name;
        $this->subject = $campaign->subject;
        $this->from_email = $campaign->from_email;
        $this->from_name = $campaign->from_name;
        $this->reply_to_email = $campaign->reply_to_email;
        $this->reply_to_name = $campaign->reply_to_name;
        $this->email_list_id = $campaign->email_list_id;
        $this->utm_tags = $campaign->utm_tags;
        $this->add_subscriber_tags = $campaign->add_subscriber_tags;
        $this->add_subscriber_link_tags = $campaign->add_subscriber_link_tags;
        $this->segment_id = $campaign->segment_id;
        $this->show_publicly = $campaign->show_publicly;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'subject' => ['nullable'],
            'from_email' => ['nullable', 'email:rfc'],
            'from_name' => 'nullable',
            'reply_to_email' => ['nullable', new Delimited('email:rfc')],
            'reply_to_name' => ['nullable', new Delimited('string')],
            'email_list_id' => Rule::exists(self::getEmailListTableName(), 'id'),
            'utm_tags' => 'bool',
            'add_subscriber_tags' => 'bool',
            'add_subscriber_link_tags' => 'bool',
            'segment_id' => ['required_if:segment,segment'],
            'show_publicly' => ['nullable', 'bool'],
        ];
    }

    public function save(string $segment): void
    {
        $this->validate();

        $this->campaign->fill(
            Arr::except($this->all(), ['campaign'])
        );

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->campaign->usingCustomSegment()) {
            $segmentClass = $this->campaign->segment_class;
        }

        $this->campaign->fill([
            'last_modified_at' => now(),
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $this->campaign->segment_id,
        ]);

        $this->campaign->save();

        $this->campaign->update(['segment_description' => $this->campaign->getSegment()->description()]);
    }
}
