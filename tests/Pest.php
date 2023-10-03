<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\MailgunWebhookConfig;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function setupImportDisk(): Filesystem
{
    Storage::fake();
    File::ensureDirectoryExists(Storage::disk(config('mailcoach.import_disk'))->path('import'));

    return Storage::disk(config('mailcoach.import_disk'));
}

expect()->extend('timePassedInSeconds', function ($expectedPassedInSeconds) {
    $actualPassedInSeconds = $this->value->diffInSeconds();

    expect($actualPassedInSeconds)->toBe($expectedPassedInSeconds);
});

function addValidSignature(array $payloadContent = []): array
{
    return array_merge(
        $payloadContent,
        [
            'signature' => [
                'timestamp' => '1529006854',
                'token' => 'a8ce0edb2dd8301dee6c2405235584e45aa91d1e9f979f3de0',
                'signature' => hash_hmac(
                    'sha256',
                    sprintf('%s%s', '1529006854', 'a8ce0edb2dd8301dee6c2405235584e45aa91d1e9f979f3de0'),
                    MailgunWebhookConfig::get()->signingSecret,
                ),
            ],
            'event-data' => [
                'event' => 'opened',
                'timestamp' => 1529006854.329574,
                'id' => 'DACSsAdVSeGpLid7TN03WA',
            ],
        ]
    );
}

function getStubs(string $name, string $dir = null): array
{
    $dir = __DIR__.'/stubs'.($dir ? "/{$dir}/" : '');

    $content = file_get_contents($dir.$name);

    return json_decode($content, true);
}

function getPostmarkStub(string $name): array
{
    return getStubs($name, 'Postmark');
}

function getMailgunStub(string $name): array
{
    return getStubs($name, 'Mailgun');
}

function getSendgridStub(string $name): array
{
    return getStubs($name, 'Sendgrid');
}

function getSendinblueStub(string $name): array
{
    return getStubs($name, 'Sendinblue');
}

function getSesStub(string $name): array
{
    return getStubs($name, 'Ses');
}
