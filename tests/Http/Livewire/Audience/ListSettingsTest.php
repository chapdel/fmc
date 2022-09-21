<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettings;
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

    \Livewire\Livewire::test(ListSettings::class, ['emailList' => $emailList])
        ->set('emailList.name', 'updated name')
        ->set('emailList.default_from_email', 'jane@example.com')
        ->set('emailList.default_from_name', 'Jane Doe')
        ->set('emailList.default_reply_to_email', 'jane@example.com')
        ->set('emailList.default_reply_to_name', 'Jane Doe')
        ->set('emailList.campaigns_feed_enabled', false)
        ->set('emailList.report_campaign_sent', false)
        ->set('emailList.report_campaign_summary', false)
        ->set('emailList.report_email_list_summary', false)
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
        'emailList.report_campaign_sent',
        'emailList.report_campaign_summary',
        'emailList.report_email_list_summary',
    ];

    foreach ($reportFields as $field) {
        $attributes = array_merge([
            'emailList.name' => 'updated name',
            'emailList.default_from_email' => 'jane@example.com',
            'emailList.default_from_name' => 'Jane Doe',
            'emailList.default_reply_to_email' => 'jane@example.com',
            'emailList.default_reply_to_name' => 'Jane Doe',
            'emailList.campaigns_feed_enabled' => false,
            'emailList.report_campaign_sent' => false,
            'emailList.report_campaign_summary' => false,
            'emailList.report_email_list_summary' => false,
            'emailList.report_recipients' => '',
        ], [
            $field => true,
        ]);

        \Livewire\Livewire::test(ListSettings::class, ['emailList' => $emailList])
            ->fill($attributes)
            ->call('save')
            ->assertHasErrors('emailList.report_recipients');
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

    \Livewire\Livewire::test(ListSettings::class, ['emailList' => $emailList])
        ->assertForbidden();
});
