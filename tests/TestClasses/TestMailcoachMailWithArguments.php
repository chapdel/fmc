<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

class TestMailcoachMailWithArguments extends MailcoachMail
{
    public $viewHtml;

    protected string $test_argument;

    public function __construct(string $test_argument)
    {
        $this->test_argument = $test_argument;
    }

    public function build()
    {
        return $this->html($this->test_argument);
    }
}
