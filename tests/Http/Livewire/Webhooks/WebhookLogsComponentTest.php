<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Database\Factories\WebhookConfigurationFactory;
use Spatie\Mailcoach\Domain\Settings\Actions\ResendWebhookCallAction;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;

beforeEach(function () {
    $this->webhook = WebhookConfigurationFactory::new()->create();
    $this->logs = WebhookLog::factory()
        ->for($this->webhook)
        ->count(5)
        ->create();
});

it('should render an overview of webhook call logs', function () {
    Livewire::test('mailcoach::webhook-logs', [
        'webhook' => $this->webhook,
    ])->assertHasNoErrors();
});

it('should be able to resend a webhook call', function () {
    $log = $this->logs->first();

    $this->partialMock(ResendWebhookCallAction::class, function ($mock) use ($log) {
        $mock->shouldReceive('execute')
            ->with($log)
            ->once();
    });

    Livewire::test('mailcoach::webhook-logs', [
        'webhook' => $this->webhook
    ])->call('resend', $log);
});
