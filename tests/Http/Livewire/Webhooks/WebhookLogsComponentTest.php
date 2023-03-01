<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Database\Factories\WebhookConfigurationFactory;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;

it('should render an overview of webhook call logs', function () {
    $webhook = WebhookConfigurationFactory::new()->create();
    WebhookLog::factory()
        ->for($webhook)
        ->count(5)
        ->create();

    Livewire::test('mailcoach::webhook-logs', [
        'webhook' => $webhook,
    ])->assertHasNoErrors();
});
