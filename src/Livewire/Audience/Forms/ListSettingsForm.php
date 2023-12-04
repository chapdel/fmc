<?php

namespace Spatie\Mailcoach\Livewire\Audience\Forms;

use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\ValidationRules\Rules\Delimited;

class ListSettingsForm extends Form
{
    #[Rule('required')]
    public string $name;

    #[Rule(['required', 'email:rfc'])]
    public ?string $default_from_email;

    #[Rule(['nullable'])]
    public ?string $default_from_name;

    #[Rule([new Delimited('email')])]
    public ?string $default_reply_to_email;

    #[Rule(['nullable'])]
    public ?string $default_reply_to_name;

    #[Rule(['boolean'])]
    public ?bool $campaigns_feed_enabled = false;

    #[Rule(['boolean'])]
    public ?bool $report_campaign_sent = false;

    #[Rule(['boolean'])]
    public ?bool $report_campaign_summary = false;

    #[Rule(['boolean'])]
    public ?bool $report_email_list_summary = false;

    #[Rule([
        new Delimited('email'),
        'required_if:report_email_list_summary,true',
        'required_if:report_campaign_sent,true',
        'required_if:report_campaign_summary,true',
    ])]
    public ?string $report_recipients;

    public EmailList $emailList;

    public function setEmailList(EmailList $emailList)
    {
        $this->emailList = $emailList;

        $this->name = $emailList->name;
        $this->default_from_email = $emailList->default_from_email;
        $this->default_from_name = $emailList->default_from_name;
        $this->default_reply_to_email = $emailList->default_reply_to_email;
        $this->default_reply_to_name = $emailList->default_reply_to_name;
        $this->campaigns_feed_enabled = $emailList->campaigns_feed_enabled;
        $this->report_campaign_sent = $emailList->report_campaign_sent;
        $this->report_campaign_summary = $emailList->report_campaign_summary;
        $this->report_email_list_summary = $emailList->report_email_list_summary;
        $this->report_recipients = $emailList->report_recipients;
    }

    public function update(): void
    {
        $this->emailList->update(Arr::except($this->all(), ['emailList']));
    }
}
