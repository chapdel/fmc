<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Faker\Generator;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;

class TestCampaignMailWithStaticHtml extends CampaignMail
{
    public $viewHtml;

    public function __construct()
    {
        $faker = app(Generator::class);

        $html = '<<<HTML
        <html>
        <style>

            body {
                background-color: #e8eff6;
                }
        </style>
        <body>My body</body>
        </html>';

        $this->viewHtml = $html;
    }

    public function build()
    {
        return $this->html($this->viewHtml);
    }
}
