<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomSubscriber;

uses(TestCase::class);

beforeEach(function () {
    test()->authenticate();

    test()->subscriber = Subscriber::factory()->create();
});

afterEach(function () {
    restoreStandardSubscriberModel();
});

it('respects custom model for route model binding', function () {
    test()->assertNotInstanceOf(CustomSubscriber::class, test()->subscriber);

    useCustomSubscriberModel();

    $detailsRoute = route("mailcoach.emailLists.subscriber.details", [test()->subscriber->emailList, test()->subscriber]);
    $response = test()->get($detailsRoute);
    $injectedModel = $response->viewData("subscriber");

    test()->assertInstanceOf(CustomSubscriber::class, $injectedModel);
});

// Helpers
function useCustomSubscriberModel()
{
    Config::set("mailcoach.models.subscriber", CustomSubscriber::class);
}

function restoreStandardSubscriberModel()
{
    Config::set("mailcoach.models.subscriber", Subscriber::class);
}
