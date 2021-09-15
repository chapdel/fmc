<?php

use Carbon\Carbon;
use Spatie\Mailcoach\Domain\Shared\Commands\CleanupProcessedFeedbackCommand;
use Spatie\WebhookClient\Models\WebhookCall;

beforeEach(function () {
    Carbon::setTestNow();
});

it('deletes only processed webhooks older than the default 1 hour interval', function () {
    $keep = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()]);
    $delete = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(2)]);

    test()->artisan(CleanupProcessedFeedbackCommand::class)->assertExitCode(0);

    test()->assertNotNull($keep->refresh());
    test()->assertDeleted($delete);
});

it('can be passed the hours interval in the command options', function () {
    $keep1 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()]);
    $keep2 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(2)]);
    $delete = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(6)]);

    test()->artisan(CleanupProcessedFeedbackCommand::class, ['--hours' => 5])->assertExitCode(0);

    test()->assertNotNull($keep1->refresh());
    test()->assertNotNull($keep2->refresh());
    test()->assertDeleted($delete);
});

it('only deletes calls that are from the feedback packages', function () {
    $delete1 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(6)]);
    $delete2 = WebhookCall::create(['name' => 'sendgrid-feedback', 'processed_at' => now()->subHours(6)]);
    $delete3 = WebhookCall::create(['name' => 'mailgun-feedback', 'processed_at' => now()->subHours(6)]);
    $delete4 = WebhookCall::create(['name' => 'postmark-feedback', 'processed_at' => now()->subHours(6)]);
    $keep = WebhookCall::create(['name' => 'stripe', 'processed_at' => now()->subHours(6)]);

    test()->artisan(CleanupProcessedFeedbackCommand::class, ['--hours' => 5])->assertExitCode(0);

    test()->assertNotNull($keep->refresh());
    test()->assertDeleted($delete1);
    test()->assertDeleted($delete2);
    test()->assertDeleted($delete3);
    test()->assertDeleted($delete4);
});
