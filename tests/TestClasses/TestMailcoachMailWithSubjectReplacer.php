<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Faker\Generator;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

class TestMailcoachMailWithSubjectReplacer extends MailcoachMail
{
    public $viewHtml;

    public function __construct()
    {
        $faker = app(Generator::class);

        $this->viewHtml = $faker->randomHtml();
    }

    public function build()
    {
        return $this->html($this->viewHtml)
                            ->subject('Custom Subject: ::customreplacer::');
    }
}
