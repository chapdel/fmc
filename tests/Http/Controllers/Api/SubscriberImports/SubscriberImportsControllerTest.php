<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\SubscriberImportsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class SubscriberImportsControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->emailList = EmailList::factory()->create();
    }

    /** @test */
    public function it_can_list_all_subscriber_imports()
    {
        $subscriberImports = SubscriberImport::factory(3)->create();

        $response = $this
            ->getJson(action([SubscriberImportsController::class, 'index']))
            ->assertSuccessful()
            ->assertJsonCount(3, 'data');

        foreach ($subscriberImports as $subscriberImport) {
            $response->assertJsonFragment(['uuid' => $subscriberImport->uuid]);
        }
    }

    /** @test */
    public function it_can_show_a_subscriber_import()
    {
        $subscriberImport = SubscriberImport::factory()->create();

        $this
            ->getJson(action([SubscriberImportsController::class, 'show'], $subscriberImport))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_subscriber_import()
    {
        $payload = [
            'subscribers_csv' => 'email' .PHP_EOL . 'john@example.com',
            'email_list_id' => $this->emailList->id,
        ];

        $this
            ->postJson(action([SubscriberImportsController::class, 'store']), $payload)
            ->assertSuccessful();

        $payload['status'] = SubscriberImportStatus::DRAFT;

        $this->assertDatabaseHas('mailcoach_subscriber_imports', $payload);
    }

    /** @test */
    public function it_can_update_a_subscriber_import()
    {
        $subscriberImport = SubscriberImport::factory()->create([
            'status' => SubscriberImportStatus::DRAFT,
        ]);

        $payload = [
            'subscribers_csv' => 'email' .PHP_EOL . 'john@example.com',
            'email_list_id' => $this->emailList->id,
        ];

        $this
            ->putJson(action([SubscriberImportsController::class, 'update'], $subscriberImport), $payload)
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_subscriber_imports', $payload);
    }

    /** @test */
    public function it_can_delete_a_subscriber_import()
    {
        $subscriberImport = SubscriberImport::factory()->create();

        $this
            ->deleteJson(action([SubscriberImportsController::class, 'destroy'], $subscriberImport))
            ->assertSuccessful();

        $this->assertCount(0, SubscriberImport::get());
    }
}
