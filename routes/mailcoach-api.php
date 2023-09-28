<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignBouncesController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendCampaignController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Http\Api\Controllers\Editors\EditorJs\RenderEditorController;
use Spatie\Mailcoach\Http\Api\Controllers\Editors\Markdown\RenderMarkdownController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\EmailListsController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\SegmentsController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ConfirmSubscriberController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResubscribeController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\UnsubscribeController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\TagsController;
use Spatie\Mailcoach\Http\Api\Controllers\SendsController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\AppendSubscriberImportController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\SubscriberImportsController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\SendTransactionalMailController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ShowTransactionalMailController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\TransactionalMailsController;

Route::apiResource('templates', TemplatesController::class)->parameter('templates', 'template');
Route::apiResource('sends', SendsController::class)->except(['store', 'update'])->parameter('sends', 'send');
Route::apiResource('campaigns', CampaignsController::class)->parameter('campaigns', 'campaign');

Route::prefix('campaigns/{campaign}')->group(function () {
    Route::post('send-test', SendTestEmailController::class);
    Route::post('send', SendCampaignController::class);

    Route::get('opens', CampaignOpensController::class);
    Route::get('clicks', CampaignClicksController::class);
    Route::get('unsubscribes', CampaignUnsubscribesController::class);
    Route::get('bounces', CampaignBouncesController::class);
});

Route::apiResource('email-lists', EmailListsController::class)->parameter('email-lists', 'emailList');
Route::apiResource('email-lists.subscribers', SubscribersController::class)->only(['index', 'store'])->parameter('email-lists', 'emailList');
Route::apiResource('email-lists.tags', TagsController::class)->parameters([
    'email-lists' => 'emailList',
    'tags' => 'tag',
]);
// @todo: Enable segments
//Route::apiResource('email-lists.segments', SegmentsController::class)->parameters([
//    'email-lists' => 'emailList',
//    'segments' => 'segment',
//]);
Route::apiResource('subscribers', SubscribersController::class)->except(['index', 'store'])->parameter('subscribers', 'subscriber');

Route::prefix('subscribers/{subscriber}')->group(function () {
    Route::post('confirm', ConfirmSubscriberController::class);
    Route::post('unsubscribe', UnsubscribeController::class);
    Route::post('resubscribe', ResubscribeController::class);

    Route::post('resend-confirmation', ResendConfirmationMailController::class);
});

Route::apiResource('subscriber-imports', SubscriberImportsController::class)->parameter('subscriber-imports', 'subscriberImport');

Route::prefix('subscriber-imports/{subscriberImport}')->group(function () {
    Route::post('append', AppendSubscriberImportController::class);
    Route::post('start', StartSubscriberImportController::class);
});

Route::prefix('transactional-mails')->group(function () {
    Route::get('/', TransactionalMailsController::class);
    Route::post('send', SendTransactionalMailController::class);
    Route::get('{transactionalMail}', ShowTransactionalMailController::class);
    Route::post('{transactionalMail}/resend', ResendTransactionalMailController::class);
});

Route::prefix('automations')->group(function () {
    Route::post('{automation}/trigger', TriggerAutomationController::class);
});

Route::post('render', '\\'.RenderEditorController::class);
Route::post('render-markdown', '\\'.RenderMarkdownController::class)->name('mailcoach-markdown-editor.render-markdown');
