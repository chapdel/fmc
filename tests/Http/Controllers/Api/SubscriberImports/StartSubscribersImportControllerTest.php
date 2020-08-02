<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\SubscriberImports;

use Spatie\Mailcoach\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class StartSubscribersImportControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private SubscriberImport $subscriberImport;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->subscriberImport = factory(SubscriberImport::class, )->create([
            'status' => SubscriberImportStatus::DRAFT,
            'subscribers_csv' => 'email' . PHP_EOL . 'john@example.com',
        ]);
    }

    /** @test */
    public function the_import_can_be_started_via_the_api()
    {
        $this
            ->postJson(action(StartSubscriberImportController::class, $this->subscriberImport))
            ->assertSuccessful();

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = $this->subscriberImport->emailList;

        $this->assertTrue($emailList->isSubscribed('john@example.com'));
        $this->assertEquals(SubscriberImportStatus::COMPLETED, $this->subscriberImport->refresh()->status);
    }
}
