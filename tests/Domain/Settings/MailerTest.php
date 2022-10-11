<?php

use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

it('can get and set configuration values', function () {
    /** @var Mailer $mailer */
    $mailer = Mailer::factory()->create();

    $mailer->merge(['a' => 'first value', 'b' => 'second value']);

    expect($mailer->get('a'))->toBe('first value');
    expect($mailer->get('b'))->toBe('second value');

    $mailer->merge(['c' => 'third value']);

    expect($mailer->get('a'))->toBe('first value');
    expect($mailer->get('b'))->toBe('second value');
    expect($mailer->get('c'))->toBe('third value');
});

it('sets config key name when creating and can find by config key name', function () {
    $mailer = Mailer::factory()->create(['name' => 'Some name']);

    expect($mailer->config_key_name)->toBe('mailcoach-some-name');
    expect($mailer->configName())->toBe('mailcoach-some-name');

    expect(Mailer::findByConfigKeyName('mailcoach-some-name')->is($mailer))->toBeTrue();
});

it('handles special characters', function ($name, $expected) {
    $mailer = Mailer::factory()->create(['name' => $name]);

    expect($mailer->config_key_name)->toBe($expected);
    expect($mailer->configName())->toBe($expected);
})->with([
    ['Some @ name', 'mailcoach-some-at-name'],
    ['With 📯 emoji', 'mailcoach-with-emoji'],
    ['Name', 'mailcoach-name'],
]);
