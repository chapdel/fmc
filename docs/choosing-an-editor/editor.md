---
title: Editor.js
weight: 2
---

[Editor.js](https://editorjs.io) is a beautiful block based wysiwyg editor. It also offers image uploads.

![screenshot](/docs/laravel-mailcoach/v4/images/editors/editor-js.png)

## Configuration via the UI

When you've installed Mailcoach as a standalone app or via the 1-click-installer you can choose this editor through the UI on the settings screen.

## Manual installation

When you've installed Mailcoach in an existing Laravel app, you must manually install this editor.

You can install the add-on package via Composer:

```bash
composer require spatie/laravel-mailcoach-editor:^0.1
```

### Publish and run the migration

```bash
php artisan vendor:publish --provider="Spatie\MailcoachEditor\MailcoachEditorServiceProvider" --tag="mailcoach-editor-migrations"
php artisan migrate
```

### Publish the assets

```bash
php artisan vendor:publish --provider="Spatie\MailcoachEditor\MailcoachEditorServiceProvider" --tag="mailcoach-editor-assets"
```

### Add the route macro

You must register the routes needed to handle uploads. We recommend that you don't put this in your routes file, but in the `boot()` method of your `RouteServiceProvider` within `$this->routes(function () { }`.

```php
Route::mailcoachEditor('mailcoachEditor');
```

### Configuring Mailcoach

Set the `mailcoach.campaign.editor` and `mailcoach.automationMail.editor` config values to `\Spatie\MailcoachEditor\Editor::class`

### Configuring the Editor

You can publish the configuration file using

```bash
php artisan vendor:publish --provider="Spatie\MailcoachEditor\MailcoachEditorServiceProvider" --tag="mailcoach-editor-config"
```

The Editor also supports image uploads, you can configure the `disk_name` in the `mailcoach-editor.php` config file.

The package does not automatically delete uploads. If you upload files and replace them, the original files will still be stored on disk.

To remove unwanted uploads, delete the relevant `Spatie\MediaLibrary\MediaCollections\Models\Media` models via code.

```php
Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId)->delete();
```

This way the files will also be deleted from the filesytem.

## Switching to and from the Editor

This Editor stores content in a structured way. When switching from or to the Editor, content in existing templates and draft campaigns might get lost.
