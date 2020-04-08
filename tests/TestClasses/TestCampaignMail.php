<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Faker\Generator;
use Spatie\Mailcoach\Mails\CampaignMail;

class TestCampaignMail extends CampaignMail
{
    public $viewHtml;

    public function __construct()
    {
        $faker = app(Generator::class);

        $this->viewHtml = $faker->randomHtml();
    }

    public function build()
    {
        return $this->html($this->viewHtml);
    }
}
