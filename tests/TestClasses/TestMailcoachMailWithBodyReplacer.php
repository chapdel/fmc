<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Faker\Generator;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;

class TestMailcoachMailWithBodyReplacer extends MailcoachMail
{
    public $viewHtml;

    public function __construct()
    {
        $faker = app(Generator::class);

        $html = '
            <html>
            <style>

                body {
                    background-color: #e8eff6;
                    }
            </style>
            <body>::customreplacer::</body>
            </html>';

        $this->viewHtml = $html;
    }

    public function build()
    {
        return $this->html($this->viewHtml);
    }
}
