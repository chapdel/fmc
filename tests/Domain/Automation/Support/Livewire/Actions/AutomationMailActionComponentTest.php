<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AutomationMailActionComponent;
use Spatie\Mailcoach\Tests\TestCase;

class AutomationMailActionComponentTest extends TestCase
{
    private array $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = [
            'class' => SendAutomationMailAction::class,
        ];
    }

    /** @test * */
    public function it_requires_automation_mail_id()
    {
        Livewire::test(AutomationMailActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('automation_mail_id', '')
          ->call('save')
          ->assertHasErrors([
            'automation_mail_id' => ['required'],
          ]);
    }

    /** @test * */
    public function it_loads_options_on_mount()
    {
        $mail = AutomationMail::factory()->create();

        Livewire::test(AutomationMailActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->assertViewHas('campaignOptions', [
            $mail->id => $mail->name
        ]);
    }

    /** @test * */
    public function it_requires_a_valid_automation_mail_id()
    {
        Livewire::test(AutomationMailActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('automation_mail_id', '1')
            ->call('save')
            ->assertHasErrors([
                'automation_mail_id' => ['exists'],
            ]);
    }

    /** @test * */
    public function it_emits_correct_data()
    {
        $uuid = Str::uuid()->toString();

        $mail = AutomationMail::factory()->create();

        Livewire::test(AutomationMailActionComponent::class, [
            'action' => $this->action,
            'uuid' => $uuid,
        ])  ->set('automation_mail_id', $mail->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted('actionSaved', $uuid, [
                'automation_mail_id' => $mail->id,
            ]);
    }
}
