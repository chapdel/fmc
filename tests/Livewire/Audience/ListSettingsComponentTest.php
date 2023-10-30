<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

it('can update the settings of an email list', function () {
    test()->authenticate();

    $emailList = EmailList::create([
        'name' => 'my list',
    ]);

    $attributes = [
        'name' => 'updated name',
        'default_from_email' => 'jane@example.com',
        'default_from_name' => 'Jane Doe',
        'default_reply_to_email' => 'jane@example.com',
        'default_reply_to_name' => 'Jane Doe',
    ];

    \Livewire\Livewire::test(ListSettingsComponent::class, ['emailList' => $emailList])
        ->set('form.name', 'updated name')
        ->set('form.default_from_email', 'jane@example.com')
        ->set('form.default_from_name', 'Jane Doe')
        ->set('form.default_reply_to_email', 'jane@example.com')
        ->set('form.default_reply_to_name', 'Jane Doe')
        ->set('form.campaigns_feed_enabled', false)
        ->set('form.report_campaign_sent', false)
        ->set('form.report_campaign_summary', false)
        ->set('form.report_email_list_summary', false)
        ->call('save')
        ->assertHasNoErrors();

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

it('requires report recipients if reports are to be sent', function () {
    test()->authenticate();

    $emailList = EmailList::create([
        'name' => 'my list',
        'campaign_mailer' => 'array',
        'transactional_mailer' => 'array',
    ]);

    $reportFields = [
        'form.report_campaign_sent',
        'form.report_campaign_summary',
        'form.report_email_list_summary',
    ];

    foreach ($reportFields as $field) {
        $attributes = array_merge([
            'form.name' => 'updated name',
            'form.default_from_email' => 'jane@example.com',
            'form.default_from_name' => 'Jane Doe',
            'form.default_reply_to_email' => 'jane@example.com',
            'form.default_reply_to_name' => 'Jane Doe',
            'form.campaigns_feed_enabled' => false,
            'form.report_campaign_sent' => false,
            'form.report_campaign_summary' => false,
            'form.report_email_list_summary' => false,
            'form.report_recipients' => '',
        ], [
            $field => true,
        ]);

        \Livewire\Livewire::test(ListSettingsComponent::class, ['emailList' => $emailList])
            ->fill($attributes)
            ->call('save')
            ->assertHasErrors('form.report_recipients');
    }
});

it('authorizes access with custom policy', function () {
    $this->withExceptionHandling();

    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    $emailList = EmailList::create([
        'name' => 'my list',
        'campaign_mailer' => 'array',
        'transactional_mailer' => 'array',
    ]);

    \Livewire\Livewire::test(ListSettingsComponent::class, ['emailList' => $emailList])
        ->assertForbidden();
});
