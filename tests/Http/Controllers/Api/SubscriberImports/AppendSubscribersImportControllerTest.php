<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\SubscriberImports;

use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\AppendSubscriberImportController;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class AppendSubscribersImportControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_append_to_the_subscribers_csv()
    {
        $initialSubscribersCsv = 'email' . PHP_EOL . 'john@example.com';

        $subscriberImport = SubscriberImport::factory()->create([
           'subscribers_csv' => $initialSubscribersCsv,
        ]);

        $payload = [
            'subscribers_csv' => 'paul@example.com',
        ];

        $this
            ->postJson(action(AppendSubscriberImportController::class, $subscriberImport), $payload)
            ->assertSuccessful();

        $expected = $initialSubscribersCsv . PHP_EOL . $payload['subscribers_csv'];

        $this->assertEquals($expected, $subscriberImport->refresh()->subscribers_csv);
    }
}
