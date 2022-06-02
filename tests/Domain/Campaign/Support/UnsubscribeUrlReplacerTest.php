<?php

use Spatie\Mailcoach\Domain\Automation\Support\Replacers\UnsubscribeUrlReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    $this->send = Send::factory()->create();
    $this->send->subscriber->addTag('a tag');
});

it('replaces unsubscribe urls', function () {
    $replacer = new UnsubscribeUrlReplacer();

    expect($replacer->replace('::unsubscribeUrl::', $this->send))->toEqual($this->send->subscriber->unsubscribeUrl($this->send));
    expect($replacer->replace(urlencode('::unsubscribeUrl::'), $this->send))->toEqual($this->send->subscriber->unsubscribeUrl($this->send));
    expect($replacer->replace('::unsubscribeTag::a tag::', $this->send))->toEqual($this->send->subscriber->unsubscribeTagUrl('a tag', $this->send));
    expect($replacer->replace(urlencode('::unsubscribeTag::a tag::'), $this->send))->toEqual($this->send->subscriber->unsubscribeTagUrl('a tag', $this->send));
});
