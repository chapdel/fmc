<?php

use Spatie\Mailcoach\Domain\Automation\Actions\ConvertHtmlToTextAction;

it('can convert html to text', function () {
    $html = file_get_contents(__DIR__ . '/stubs/newsletterHtml.txt');

    $text = (new ConvertHtmlToTextAction())->execute($html);

    test()->assertMatchesSnapshot($text);
});
