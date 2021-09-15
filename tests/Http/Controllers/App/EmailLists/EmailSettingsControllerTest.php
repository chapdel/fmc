<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

uses(TestCase::class);

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

    $this
        ->put(
            action([EmailListGeneralSettingsController::class, 'update'], $emailList->id),
            $attributes
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect(action([EmailListGeneralSettingsController::class, 'edit'], $emailList->id));

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
        'report_campaign_sent',
        'report_campaign_summary',
        'report_email_list_summary',
    ];

    foreach ($reportFields as $field) {
        $attributes = [
            'name' => 'updated name',
            'default_from_email' => 'jane@example.com',
            'default_from_name' => 'Jane Doe',
            'default_reply_to_email' => 'jane@example.com',
            'default_reply_to_name' => 'Jane Doe',
            'campaign_mailer' => 'log',
            'transactional_mailer' => 'log',
            'report_recipients' => '',
            $field => "1",
        ];

        $this
            ->withExceptionHandling()
            ->put(
                action([EmailListGeneralSettingsController::class, 'update'], $emailList->id),
                $attributes
            )
            ->assertSessionHasErrors(["report_recipients"]);
    }
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    $emailList = EmailList::create([
        'name' => 'my list',
        'campaign_mailer' => 'array',
        'transactional_mailer' => 'array',
    ]);

    $this
        ->withExceptionHandling()
        ->get(action([EmailListGeneralSettingsController::class, 'edit'], $emailList->id))
        ->assertForbidden();

    $this
        ->withExceptionHandling()
        ->put(route('mailcoach.emailLists.general-settings', $emailList), validUpdateData())
        ->assertForbidden();
});

// Helpers
function validUpdateData()
{
    return [
        'name' => 'Jane',
        'default_from_email' => 'jane@example.com',
    ];
}
