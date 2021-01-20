<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

class EmailListPolicyTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();
    }

    /** @test */
    public function it_uses_default_policy()
    {
        Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
        $jane = (new User())->forceFill(['email' => 'jane@example.com']);
        $john = (new User())->forceFill(['email' => 'john@example.com']);

        $this->assertInstanceOf(EmailListPolicy::class, Gate::getPolicyFor($this->emailList));
        $this->assertTrue($jane->can("create", EmailList::class));
        $this->assertFalse($john->can("create", EmailList::class));
    }

    /** @test */
    public function it_uses_custom_policy()
    {
        Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
        $jane = (new User())->forceFill(['email' => 'jane@example.com']);

        app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

        $this->assertInstanceOf(CustomEmailListDenyAllPolicy::class, Gate::getPolicyFor($this->emailList));
        $this->assertFalse($jane->can("create", EmailList::class));

        $this
            ->postCreateList($jane)
            ->assertForbidden();
    }

    /** @test */
    public function it_authorizes_relevant_routes()
    {
        Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
        $jane = (new User())->forceFill(['email' => 'jane@example.com']);

        app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

        $this
            ->postCreateList($jane)
            ->assertForbidden();
    }

    private function postCreateList(Authorizable $asUser)
    {
        return $this
            ->withExceptionHandling()
            ->actingAs($asUser)
            ->post(action(CreateEmailListController::class), [
                'name' => 'new list',
                'default_from_email' => 'john@example.com',
            ]);
    }
}
