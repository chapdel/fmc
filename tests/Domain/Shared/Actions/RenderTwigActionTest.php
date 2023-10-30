<?php

use Spatie\Mailcoach\Domain\Shared\Actions\RenderTwigAction;
use Spatie\Mailcoach\Mailcoach;

it('decodes characters to work', function () {
    $action = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class);

    expect($action->execute('%7B%7BunsubscribeUrl%7D%7D', [
        'unsubscribeUrl' => 'https://spatie.be',
    ]))->toEqual('https://spatie.be');
});

it('keeps certain characters', function () {
    $action = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class);

    expect($action->execute('Hello, this is some text with a +'))->toEqual('Hello, this is some text with a +');
});
