<?php

namespace Spatie\Mailcoach;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Models\Campaign;

class MailcoachRegistrar
{
    /** @var string */
    protected $campaignClass;

    /**
     * MailcoachRegistrar constructor.
     *
     */
    public function __construct()
    {
        $this->campaignClass = config('mailcoach.models.campaign');
    }

    /**
     * Get an instance of the campaign class.
     *
     * @return \Spatie\Mailcoach\Model\Campaign
     */
    public function getCampaignClass(): Campaign
    {
        return app($this->campaignClass);
    }

    public function setCampaignClass($campaignClass)
    {
        $this->campaignClass = $campaignClass;

        return $this;
    }
}
