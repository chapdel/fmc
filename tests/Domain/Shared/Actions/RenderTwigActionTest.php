<?php

use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;

it('decodes characters to work', function () {
    $action = app(RenderTwigAction::class);

    expect($action->execute('%7B%7BunsubscribeUrl%7D%7D', [
        'unsubscribeUrl' => 'https://spatie.be',
    ]))->toEqual('https://spatie.be');
});

it('keeps certain characters', function () {
    $action = app(RenderTwigAction::class);

    expect($action->execute('Hello, this is some text with a +'))->toEqual('Hello, this is some text with a +');
});
