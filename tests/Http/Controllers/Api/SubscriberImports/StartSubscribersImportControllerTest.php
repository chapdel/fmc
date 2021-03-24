<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class StartSubscribersImportControllerTest extends TestCase
{
    use RespondsToApiRequests;

    protected SubscriberImport $subscriberImport;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->subscriberImport = SubscriberImport::factory()->create([
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

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = $this->subscriberImport->emailList;

        $this->assertTrue($emailList->isSubscribed('john@example.com'));
        $this->assertEquals(SubscriberImportStatus::COMPLETED, $this->subscriberImport->refresh()->status);
    }
}
