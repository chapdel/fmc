<?php
declare(strict_types=1);

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists\Subscribers;

use Illuminate\Support\Facades\Config;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomSubscriber;

class SubscriberDetailsControllerTest extends TestCase
{
    protected Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->subscriber = Subscriber::factory()->create();
    }

    public function tearDown(): void
    {
        $this->restoreStandardSubscriberModel();

        parent::tearDown();
    }

    /** @test */
    public function it_respects_custom_model_for_route_model_binding()
    {
        $this->assertNotInstanceOf(CustomSubscriber::class, $this->subscriber);

        $this->useCustomSubscriberModel();

        $detailsRoute = route("mailcoach.emailLists.subscriber.details", [$this->subscriber->emailList, $this->subscriber]);
        $response = $this->get($detailsRoute);
        $injectedModel = $response->viewData("subscriber");

        $this->assertInstanceOf(CustomSubscriber::class, $injectedModel);
    }

    protected function useCustomSubscriberModel()
    {
        Config::set("mailcoach.models.subscriber", CustomSubscriber::class);
    }

    protected function restoreStandardSubscriberModel()
    {
        Config::set("mailcoach.models.subscriber", Subscriber::class);
    }
}
