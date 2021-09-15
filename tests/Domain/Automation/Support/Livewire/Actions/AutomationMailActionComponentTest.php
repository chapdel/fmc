<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AutomationMailActionComponent;
use Spatie\Mailcoach\Tests\TestCase;



beforeEach(function () {
    test()->action = [
        'class' => SendAutomationMailAction::class,
    ];
});

it('requires automation mail id', function () {
    Livewire::test(AutomationMailActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('automation_mail_id', '')
      ->call('save')
      ->assertHasErrors([
        'automation_mail_id' => ['required'],
      ]);
});

it('loads options on mount', function () {
    $mail = AutomationMail::factory()->create();

    Livewire::test(AutomationMailActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->assertViewHas('campaignOptions', [
        $mail->id => $mail->name,
    ]);
});

it('requires a valid automation mail id', function () {
    Livewire::test(AutomationMailActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('automation_mail_id', '1')
        ->call('save')
        ->assertHasErrors([
            'automation_mail_id' => ['exists'],
        ]);
});

it('emits correct data', function () {
    $uuid = Str::uuid()->toString();

    $mail = AutomationMail::factory()->create();

    Livewire::test(AutomationMailActionComponent::class, [
        'action' => test()->action,
        'uuid' => $uuid,
    ])  ->set('automation_mail_id', $mail->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertEmitted('actionSaved', $uuid, [
            'automation_mail_id' => $mail->id,
        ]);
});
